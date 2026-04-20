<?php

namespace App\Services\Admin;

use App\Models\BillingOrder;
use App\Models\Group;
use App\Models\Report;
use App\Models\User;
use App\Services\AdminAccessService;
use App\Services\PluginManager;
use App\Services\ThemeManager;
use App\Support\SubscriptionSettings;
use App\Support\SystemVersion;
use Illuminate\Support\Facades\Cache;

class AdminNotificationService
{
    public function __construct(
        private readonly AdminAccessService $adminAccess,
        private readonly PluginManager $pluginManager,
        private readonly ThemeManager $themeManager
    ) {
    }

    /**
     * Get all pending notifications for an administrator.
     *
     * @param User $user
     * @return array
     */
    public function getNotifications(User $user): array
    {
        $notifications = [];

        // 1. Billing Orders (if enabled and user has access)
        if (SubscriptionSettings::isEnabled() && $this->adminAccess->canAccess($user, null, 'billing')) {
            $billingCount = BillingOrder::where('status', BillingOrder::STATUS_PENDING_REVIEW)->count();

            if ($billingCount > 0) {
                $notifications[] = [
                    'id' => 'billing',
                    'count' => $billingCount,
                    'label' => __('messages.new_billing_orders', ['count' => $billingCount]),
                    'icon' => 'feather-credit-card',
                    'url' => route('admin.billing.orders'),
                    'module' => 'billing'
                ];
            }
        }

        // 2. Reports (if user has access to community module)
        if ($this->adminAccess->canAccess($user, null, 'community')) {
            $reportCount = Report::where('statu', 1)->count();
            if ($reportCount > 0) {
                $notifications[] = [
                    'id' => 'reports',
                    'count' => $reportCount,
                    'label' => __('messages.new_reports', ['count' => $reportCount]),
                    'icon' => 'feather-alert-triangle',
                    'url' => route('admin.reports'),
                    'module' => 'community'
                ];
            }

            // Groups Pending Review
            $groupCount = Group::where('status', Group::STATUS_PENDING_REVIEW)->count();
            if ($groupCount > 0) {
                $notifications[] = [
                    'id' => 'groups',
                    'count' => $groupCount,
                    'label' => __('messages.new_groups_pending', ['count' => $groupCount]),
                    'icon' => 'feather-users',
                    'url' => route('admin.groups.index'),
                    'module' => 'community'
                ];
            }
        }

        // 3. System Updates (if user has access to updates module)
        if ($this->adminAccess->canAccess($user, null, 'updates')) {
            $currentVersion = SystemVersion::CURRENT;
            $latestRelease = Cache::get('github_latest_release');
            
            if ($latestRelease) {
                $latestVersion = ltrim($latestRelease['tag_name'] ?? '', 'v');
                if (version_compare($latestVersion, $currentVersion, '>')) {
                    $notifications[] = [
                        'id' => 'system_update',
                        'count' => 1,
                        'label' => __('messages.new_system_update'),
                        'icon' => 'feather-download-cloud',
                        'url' => route('admin.updates'),
                        'module' => 'updates'
                    ];
                }
            }
        }

        // 4. Plugin Updates
        if ($this->adminAccess->canAccess($user, null, 'plugins')) {
            $pluginUpdates = $this->pluginManager->checkForUpdates();
            $pluginUpdateCount = is_array($pluginUpdates) ? count($pluginUpdates) : 0;
            if ($pluginUpdateCount > 0) {
                $notifications[] = [
                    'id' => 'plugin_updates',
                    'count' => $pluginUpdateCount,
                    'label' => __('messages.new_plugin_updates', ['count' => $pluginUpdateCount]),
                    'icon' => 'feather-package',
                    'url' => route('admin.plugins'),
                    'module' => 'plugins'
                ];
            }
        }

        // 5. Theme Updates
        if ($this->adminAccess->canAccess($user, null, 'themes')) {
            $themeUpdates = $this->themeManager->checkForUpdates();
            $themeUpdateCount = is_array($themeUpdates) ? count($themeUpdates) : 0;
            if ($themeUpdateCount > 0) {
                $notifications[] = [
                    'id' => 'theme_updates',
                    'count' => $themeUpdateCount,
                    'label' => __('messages.new_theme_updates', ['count' => $themeUpdateCount]),
                    'icon' => 'feather-layout',
                    'url' => route('admin.themes'),
                    'module' => 'themes'
                ];
            }
        }

        return $notifications;
    }
}
