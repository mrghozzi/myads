<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = Notification::where('uid', $user->id)
            ->orderBy('time', 'desc')
            ->paginate(20);

        return view('theme::notifications.index', compact('notifications'));
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
}
