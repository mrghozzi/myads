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

    private function resolveUser(string|int $identifier): ?User
    {
        return User::where('username', $identifier)->first()
            ?? User::resolvePublicIdentifier($identifier)
            ?? User::find($identifier);
    }

    public function create($identifier)
    {
        $user = $this->resolveUser($identifier);
        abort_if(!$user, 404);
        
        if (Auth::id() === $user->id) {
            return back()->with('error', __('messages.cannot_block_self') ?? 'You cannot block yourself.');
        }

        return view('theme::profile.block_create', compact('user'));
    }

    public function store(Request $request, $identifier)
    {
        $request->validate([
            'block_type' => 'required|in:messages_only,full_platform',
            'duration' => 'nullable|integer|min:1', // in days
        ]);

        $user = Auth::user();
        $target = $this->resolveUser($identifier);
        if (!$target && $request->filled('user_id')) {
            $target = $this->resolveUser($request->input('user_id'));
        }
        abort_if(!$target, 404);

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

    public function destroy(Request $request, $identifier)
    {
        $user = Auth::user();
        $target = $this->resolveUser($identifier);
        abort_if(!$target, 404);

        $this->blockService->unblockUser($user, $target);

        return back()->with('success', __('messages.user_unblocked_successfully'));
    }
}
