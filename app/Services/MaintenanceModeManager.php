<?php

namespace App\Services;

use App\Models\User;
use App\Support\MaintenanceSettings;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class MaintenanceModeManager
{
    public const BYPASS_COOKIE = 'myads_maintenance_bypass';
    public const BYPASS_QUERY_KEY = 'maintenance_access';
    public const BYPASS_HEADER = 'X-Maintenance-Access';
    public const LOG_CHANNEL = 'stack';
    public const RETRY_AFTER_SECONDS = 3600;

    public function settings(): array
    {
        return MaintenanceSettings::all();
    }

    public function isEnabled(): bool
    {
        return (bool) MaintenanceSettings::get('enabled', 0);
    }

    public function retryAfter(): int
    {
        return self::RETRY_AFTER_SECONDS;
    }

    public function saveAdminSettings(array $values, ?UploadedFile $logo, ?User $actor = null): array
    {
        $current = $this->settings();
        $now = time();
        $targetEnabled = ! empty($values['enabled']);
        $payload = [
            'message' => trim((string) ($values['message'] ?? $current['message'])),
            'logo_path' => $current['logo_path'],
            'last_changed_at' => $now,
            'last_changed_by' => (int) ($actor?->getKey() ?? 0),
            'last_source' => 'admin_panel',
        ];

        if (! empty($values['remove_logo'])) {
            $this->deleteLogo($current['logo_path']);
            $payload['logo_path'] = '';
        }

        if ($logo instanceof UploadedFile) {
            $payload['logo_path'] = $this->storeLogo($logo, $current['logo_path']);
        }

        MaintenanceSettings::save($payload);

        if ((bool) $current['enabled'] !== $targetEnabled) {
            return $targetEnabled
                ? $this->enable($actor, 'admin_panel')
                : $this->disable($actor, 'admin_panel');
        }

        Log::channel(self::LOG_CHANNEL)->info('Maintenance settings updated.', [
            'enabled' => (bool) $current['enabled'],
            'actor_id' => (int) ($actor?->getKey() ?? 0),
            'source' => 'admin_panel',
            'timestamp' => $now,
        ]);

        return $this->settings();
    }

    public function enable(?User $actor = null, string $source = 'manual'): array
    {
        $current = $this->settings();
        $now = time();

        $message = $current['message'];
        if (in_array($source, ['plugin_activation', 'plugin_deactivation', 'plugin_upload', 'plugin_upgrade', 'theme_activation', 'theme_upgrade', 'clear_cache', 'run_migrations', 'db_repair', 'manual_update'])) {
            $message = __('messages.maintenance_auto_message');
        }

        MaintenanceSettings::save([
            'enabled' => 1,
            'enabled_at' => $now,
            'enabled_by' => (int) ($actor?->getKey() ?? 0),
            'message' => $message,
            'last_changed_at' => $now,
            'last_changed_by' => (int) ($actor?->getKey() ?? 0),
            'last_source' => $source,
        ]);

        Log::channel(self::LOG_CHANNEL)->notice('Maintenance mode enabled.', [
            'actor_id' => (int) ($actor?->getKey() ?? 0),
            'source' => $source,
            'timestamp' => $now,
            'was_enabled' => (bool) $current['enabled'],
        ]);

        return $this->settings();
    }

    public function disable(?User $actor = null, string $source = 'manual'): array
    {
        $current = $this->settings();
        $now = time();

        // If it was an auto-maintenance, restore the original message
        $payload = [
            'enabled' => 0,
            'last_changed_at' => $now,
            'last_changed_by' => (int) ($actor?->getKey() ?? 0),
            'last_source' => $source,
        ];

        if (str_contains($source, '_success') || str_contains($source, '_failed') || str_contains($source, '_error')) {
            $payload['message'] = (string) \App\Models\Option::where('o_type', 'maintenance_settings')->where('name', 'message')->first()?->o_valuer;
        }

        MaintenanceSettings::save($payload);

        Log::channel(self::LOG_CHANNEL)->notice('Maintenance mode disabled.', [
            'actor_id' => (int) ($actor?->getKey() ?? 0),
            'source' => $source,
            'timestamp' => $now,
            'was_enabled' => (bool) $current['enabled'],
        ]);

        return $this->settings();
    }

    public function emergencyAccessAllowed(Request $request): bool
    {
        $token = $this->emergencyToken();

        if ($token === '') {
            return false;
        }

        $cookieSignature = (string) $request->cookie(self::BYPASS_COOKIE, '');
        if ($cookieSignature !== '' && hash_equals($this->signedEmergencyToken($token), $cookieSignature)) {
            return true;
        }

        $provided = trim((string) ($request->query(self::BYPASS_QUERY_KEY, '') ?: $request->header(self::BYPASS_HEADER, '')));

        return $provided !== '' && hash_equals($token, $provided);
    }

    public function emergencyAccessCookieValue(): ?string
    {
        $token = $this->emergencyToken();

        if ($token === '') {
            return null;
        }

        return $this->signedEmergencyToken($token);
    }

    private function storeLogo(UploadedFile $logo, ?string $existingPath = null): string
    {
        $directory = base_path('upload/maintenance');
        if (! File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $extension = strtolower((string) ($logo->getClientOriginalExtension() ?: $logo->extension() ?: 'png'));
        $filename = 'maintenance-logo-' . time() . '-' . bin2hex(random_bytes(5)) . '.' . $extension;
        $logo->move($directory, $filename);

        $this->deleteLogo($existingPath);

        return 'upload/maintenance/' . $filename;
    }

    private function deleteLogo(?string $path): void
    {
        $relativePath = trim((string) $path);
        if ($relativePath === '') {
            return;
        }

        $absolutePath = base_path(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath));
        if (File::exists($absolutePath)) {
            File::delete($absolutePath);
        }
    }

    private function emergencyToken(): string
    {
        return trim((string) env('MAINTENANCE_EMERGENCY_TOKEN', ''));
    }

    private function signedEmergencyToken(string $token): string
    {
        return hash_hmac('sha256', $token, (string) config('app.key', 'myads-maintenance'));
    }
}
