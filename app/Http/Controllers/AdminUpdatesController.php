<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use App\Models\Option;

class AdminUpdatesController extends Controller
{
    /**
     * Current system version (hardcoded).
     */
    private const CURRENT_VERSION = '4.0.0';

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
            'latestRelease'
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
        try {
            // Fetch release data
            $releaseData = $this->fetchLatestRelease();

            if (!$releaseData) {
                return redirect()->route('admin.updates')->with('error',
                    __('messages.update_fetch_failed') ?? 'Could not fetch release information from GitHub.');
            }

            $latestVersion = ltrim($releaseData['tag_name'] ?? '', 'v');

            if (!version_compare($latestVersion, self::CURRENT_VERSION, '>')) {
                return redirect()->route('admin.updates')->with('info',
                    __('messages.already_up_to_date') ?? 'Your system is already up to date.');
            }

            // Get the download URL (prefer asset zip, fallback to zipball)
            $downloadUrl = $this->getAssetDownloadUrl($releaseData);

            if (!$downloadUrl) {
                return redirect()->route('admin.updates')->with('error',
                    __('messages.no_download_url') ?? 'No download package found in the release.');
            }

            // Download the zip file
            $response = Http::withHeaders([
                'User-Agent' => 'MyAds-Updater/1.0',
                'Accept'     => 'application/octet-stream',
            ])->timeout(120)->get($downloadUrl);

            if (!$response->successful()) {
                return redirect()->route('admin.updates')->with('error',
                    __('messages.download_failed') ?? 'Failed to download the update package.');
            }

            $tempZipPath = storage_path('app/myads_update.zip');
            File::put($tempZipPath, $response->body());

            // Extract the zip
            $zip = new \ZipArchive;
            if ($zip->open($tempZipPath) !== true) {
                File::delete($tempZipPath);
                return redirect()->route('admin.updates')->with('error',
                    __('messages.zip_open_failed') ?? 'Failed to open the update package.');
            }

            $extractPath = base_path();
            $zip->extractTo($extractPath);
            $zip->close();

            // Run update script if exists
            $updateScript = base_path('requests/update.php');
            if (File::exists($updateScript)) {
                include_once $updateScript;
            }

            // Run migrations and clear caches
            Artisan::call('migrate', ['--force' => true]);
            Artisan::call('cache:clear');
            Artisan::call('view:clear');
            Artisan::call('config:clear');

            // Clean up
            File::delete($tempZipPath);

            // Clear version cache
            Cache::forget('github_latest_release');
            Cache::forget('system_version_checked');

            return redirect()->route('admin.updates')->with('success',
                __('messages.update_success') ?? 'System updated successfully to v' . $latestVersion . '!');

        } catch (\Exception $e) {
            // Clean up on failure
            $tempZipPath = storage_path('app/myads_update.zip');
            if (File::exists($tempZipPath)) {
                File::delete($tempZipPath);
            }

            return redirect()->route('admin.updates')->with('error',
                (__('messages.update_failed') ?? 'Update failed: ') . $e->getMessage());
        }
    }

    /**
     * Fetch the latest release from GitHub API (cached).
     */
    private function fetchLatestRelease(): ?array
    {
        return Cache::remember('github_latest_release', 3600, function () {
            try {
                $response = Http::withHeaders([
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
