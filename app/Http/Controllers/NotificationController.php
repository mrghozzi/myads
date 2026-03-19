<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $notifications = $this->notificationQuery($user->id)->paginate(20);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'html' => view('theme::notifications.partials.items', [
                    'notifications' => $notifications,
                ])->render(),
                'next_page_url' => $notifications->nextPageUrl(),
            ]);
        }

        $unreadNotificationCount = $this->unreadNotificationCount($user->id);

        return view('theme::notifications.index', compact('notifications', 'unreadNotificationCount'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $notification = Notification::where('id', $id)
            ->where('uid', $user->id)
            ->firstOrFail();

        if ($notification->state != 1) {
            $notification->state = 1;
            $notification->save();
        }

        if ($notification->nurl) {
            return redirect(url($notification->nurl));
        }

        return redirect()->route('notifications.index');
    }

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        Notification::where('uid', $user->id)
            ->whereIn('state', [0, 3])
            ->update(['state' => 1]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => $this->unreadNotificationCount($user->id),
            ]);
        }

        return back()->with('success', __('messages.all_marked_read') ?? 'All notifications marked as read.');
    }

    protected function notificationQuery(int $userId)
    {
        return Notification::where('uid', $userId)
            ->orderBy('time', 'desc');
    }

    protected function unreadNotificationCount(int $userId): int
    {
        return Notification::where('uid', $userId)
            ->whereIn('state', [0, 3])
            ->count();
    }
}
