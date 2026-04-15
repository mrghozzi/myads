<?php

namespace App\Http\View\Composers;

use App\Services\Admin\AdminNotificationService;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class AdminNotificationComposer
{
    public function __construct(
        protected AdminNotificationService $notificationService
    ) {
    }

    /**
     * Bind data to the view.
     *
     * @param View $view
     * @return void
     */
    public function compose(View $view)
    {
        $user = Auth::user();
        $notifications = [];

        if ($user && $user->hasAdminAccess()) {
            $notifications = $this->notificationService->getNotifications($user);
        }

        $view->with('adminNotifications', $notifications);
    }
}
