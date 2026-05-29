<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserBlock;
use App\Services\UserBlockService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserBlockController extends Controller
{
    public function __construct(
        private readonly UserBlockService $blockService,
        private readonly NotificationService $notifications
    ) {
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $blocks = $this->blockService->getBlockedUsersFor($user);

        return view('theme::profile.blocks', compact('user', 'blocks'));
    }

    public function create($id)
    {
        $user = \App\Models\User::findOrFail($id);
        
        if (Auth::id() === $user->id) {
            return back()->with('error', __('messages.cannot_block_self') ?? 'You cannot block yourself.');
        }

        return view('theme::profile.block_create', compact('user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'block_type' => 'required|in:messages_only,full_platform',
            'duration' => 'nullable|integer|min:1', // in days
        ]);

        $user = Auth::user();
        $target = User::findOrFail($request->user_id);

        if ($user->id === $target->id) {
            return back()->withErrors(['block' => 'You cannot block yourself.']);
        }

        if ($target->isAdmin()) {
            return back()->withErrors(['block' => 'You cannot block an administrator.']);
        }

        $this->blockService->blockUser(
            $user, 
            $target, 
            $request->block_type, 
            $request->duration
        );

        // Notify the target user
        $this->notifications->send(
            $target,
            __('messages.user_blocked_you', ['user' => $user->username]),
            '#',
            'ban',
            $user->id,
            'user_blocked',
            false
        );

        return back()->with('success', __('messages.user_blocked_successfully'));
    }

    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        $target = User::findOrFail($id);

        $this->blockService->unblockUser($user, $target);

        return back()->with('success', __('messages.user_unblocked_successfully'));
    }
}
