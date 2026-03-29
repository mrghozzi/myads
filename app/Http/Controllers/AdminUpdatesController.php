<?php

namespace App\Http\Controllers;

use App\Support\SystemVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Option;
use App\Services\MaintenanceModeManager;
use App\Services\ReleaseUpdateService;
use App\Services\UpdateSafetyService;

class AdminUpdatesController extends Controller
{
    /**
     * Current system version.
     */
    public const CURRENT_VERSION = SystemVersion::CURRENT;

    public function __construct(
        private readonly UpdateSafetyService $updateSafety,
        private readonly MaintenanceModeManager $maintenanceMode,
        private readonly ReleaseUpdateService $releaseUpdate
    ) {
    }

    /**
     * GitHub repo for releases.
     */
    private const GITHUB_REPO = 'mrghozzi/myads';

    /**
     * GitHub API URL for latest release.
     */
    private const GITHUB_API_URL = 'https://api.github.com/repos/mrghozzi/myads/releases/latest';

    /**
     * GitHub API URL for all releases.
     */
    private const GITHUB_RELEASES_URL = 'https://api.github.com/repos/mrghozzi/myads/releases';

    /**
     * Display the updates page.
     */
    public function index(Request $request)
    {
        $currentVersion = self::CURRENT_VERSION;
        $preflightReport = $this->updateSafety->run();
        $maintenanceSettings = $this->maintenanceMode->settings();

        // Sync version in DB
        $this->syncVersionInDb($currentVersion);

        // Fetch latest release data from GitHub (cached for 1 hour)
        $releaseData = $this->fetchLatestRelease();

        $latestVersion = null;
        $updateAvailable = false;
        $latestRelease = null;

        if ($releaseData) {
            $latestVersion = ltrim($releaseData['tag_name'] ?? '', 'v');
            $updateAvailable = version_compare($latestVersion, $currentVersion, '>');

            $latestRelease = [
                'tag'          => $releaseData['tag_name'] ?? '',
                'name'         => $releaseData['name'] ?? $releaseData['tag_name'] ?? '',
                'body'         => $releaseData['body'] ?? '',
                'published_at' => $releaseData['published_at'] ?? null,
                'html_url'     => $releaseData['html_url'] ?? '',
                'zipball_url'  => $releaseData['zipball_url'] ?? '',
                'download_url' => $this->getAssetDownloadUrl($releaseData),
                'download_size' => $this->getAssetSize($releaseData),
            ];
        }

        return view('theme::admin.updates', compact(
            'currentVersion',
            'latestVersion',
            'updateAvailable',
            'latestRelease',
            'preflightReport',
            'maintenanceSettings'
        ));
    }

    /**
     * AJAX: Force check for updates (clears cache).
     */
    public function checkUpdate(Request $request)
    {
        // Clear the cached release data
        Cache::forget('github_latest_release');

        $currentVersion = self::CURRENT_VERSION;
        $releaseData = $this->fetchLatestRelease();

        if (!$releaseData) {
            return response()->json([
                'success'         => false,
                'message'         => __('messages.update_check_failed') ?? 'Could not connect to GitHub. Please try again later.',
                'currentVersion'  => $currentVersion,
            ]);
        }

        $latestVersion = ltrim($releaseData['tag_name'] ?? '', 'v');
        $updateAvailable = version_compare($latestVersion, $currentVersion, '>');

        return response()->json([
            'success'         => true,
            'currentVersion'  => $currentVersion,
            'latestVersion'   => $latestVersion,
            'updateAvailable' => $updateAvailable,
            'release'         => [
                'tag'           => $releaseData['tag_name'] ?? '',
                'name'          => $releaseData['name'] ?? $releaseData['tag_name'] ?? '',
                'body'          => $releaseData['body'] ?? '',
                'published_at'  => $releaseData['published_at'] ?? null,
                'html_url'      => $releaseData['html_url'] ?? '',
                'download_url'  => $this->getAssetDownloadUrl($releaseData),
                'download_size' => $this->getAssetSize($releaseData),
            ],
        ]);
    }

    /**
     * Process the update: download, extract, migrate.
     */
    public function update(Request $request)
    {
        $request->validate([
            'backup_ack_database' => ['accepted'],
            'backup_ack_files' => ['accepted'],
        ], [
            'backup_ack_database.accepted' => __('messages.backup_ack_database_required'),
            'backup_ack_files.accepted' => __('messages.backup_ack_files_required'),
        ]);

        $environmentReport = $this->updateSafety->run();

        if (! $environmentReport->isSafe()) {
            return redirect()->route('admin.updates')->with('error', __('messages.update_blocked_preflight', [
                'details' => implode(' ', $environmentReport->failureMessages()),
            ]));
        }

        $releaseData = $this->fetchLatestRelease();

        if (! $releaseData) {
            return redirect()->route('admin.updates')->with('error', __('messages.update_fetch_failed'));
        }

        $latestVersion = ltrim((string) ($releaseData['tag_name'] ?? ''), 'v');
        if (! version_compare($latestVersion, self::CURRENT_VERSION, '>')) {
            return redirect()->route('admin.updates')->with('info', __('messages.already_up_to_date'));
        }

        try {
            $this->maintenanceMode->enable($request->user(), 'admin_update_start');
            $appliedVersion = $this->releaseUpdate->applyLatestRelease(self::CURRENT_VERSION, $releaseData);
            $this->maintenanceMode->disable($request->user(), 'admin_update_success');

            return redirect()->route('admin.updates')->with('success', __('messages.update_success', [
                'version' => $appliedVersion,
            ]));
        } catch (\Throwable $exception) {
            if (! $this->maintenanceMode->isEnabled()) {
                $this->maintenanceMode->enable($request->user(), 'admin_update_failure_recovery');
            }

            return redirect()->route('admin.updates')->with('error', __('messages.update_failed_maintenance_kept', [
                'error' => $exception->getMessage(),
            ]));
        }
    }

    /**
     * Fetch the latest release from GitHub API (cached).
     */
    private function fetchLatestRelease(): ?array
    {
        return Cache::remember('github_latest_release', 3600, function () {
            try {
                $response = Http::withoutVerifying()->withHeaders([
                    'User-Agent' => 'MyAds-Updater/1.0',
                    'Accept'     => 'application/vnd.github.v3+json',
                ])->timeout(10)->get(self::GITHUB_API_URL);

                if ($response->successful()) {
                    return $response->json();
                }
            } catch (\Exception $e) {
                // Silently fail
            }
            return null;
        });
    }

    /**
     * Get the download URL from a release (prefer .zip asset, fallback to zipball_url).
     */
    private function getAssetDownloadUrl(array $releaseData): ?string
    {
        // First, look for a .zip asset
        $assets = $releaseData['assets'] ?? [];
        foreach ($assets as $asset) {
            if (str_ends_with($asset['name'] ?? '', '.zip')) {
                return $asset['browser_download_url'] ?? null;
            }
        }

        // Fallback to zipball
        return $releaseData['zipball_url'] ?? null;
    }

    /**
     * Get download size from the release assets.
     */
    private function getAssetSize(array $releaseData): ?int
    {
        $assets = $releaseData['assets'] ?? [];
        foreach ($assets as $asset) {
            if (str_ends_with($asset['name'] ?? '', '.zip')) {
                return $asset['size'] ?? null;
            }
        }
        return null;
    }

    /**
     * Sync version in the options table.
     */
    private function syncVersionInDb(string $version): void
    {
        try {
            $versionParts = explode('.', $version);
            $versionName = implode('-', $versionParts);

            $option = Option::where('o_type', 'version')->first();

            if ($option) {
                if ($option->o_valuer != $version) {
                    $option->update([
                        'o_valuer' => $version,
                        'name'     => $versionName,
                    ]);
                }
            } else {
                Option::create([
                    'name'     => $versionName,
                    'o_valuer' => $version,
                    'o_type'   => 'version',
                    'o_parent' => 0,
                    'o_order'  => 0,
                    'o_mode'   => '0',
                ]);
            }
        } catch (\Exception $e) {
            // Ignore DB errors
        }
    }
}
