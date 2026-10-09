<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Status;
use App\Models\Like;
use App\Http\Resources\UserProfileResource;
use App\Http\Resources\StatusResource;

class ProfileController extends Controller
{
    public function show($identifier)
    {
        if ($identifier === 'me') {
            $user = Auth::guard('sanctum')->user();
        } else {
            $user = User::resolvePublicIdentifier($identifier) ?: User::where('username', $identifier)->first();
        }

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return new UserProfileResource($user);
    }

    public function statuses($identifier, Request $request)
    {
        if ($identifier === 'me') {
            $user = Auth::guard('sanctum')->user();
        } else {
            $user = User::resolvePublicIdentifier($identifier) ?: User::where('username', $identifier)->first();
        }

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $activityService = app(\App\Services\StatusActivityService::class);

        // Basic fetch of user statuses
        $statuses = Status::visible()
            ->where('uid', $user->id)
            ->orderBy('date', 'desc')
            ->paginate(20);

        $activityService->decorateMany($statuses);

        return StatusResource::collection($statuses);
    }


    public function follow($identifier)
    {
        if ($identifier === 'me') {
            return response()->json(['error' => 'You cannot follow yourself'], 400);
        }

        $targetUser = User::resolvePublicIdentifier($identifier) ?: User::where('username', $identifier)->first();

        if (!$targetUser) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $currentUser = Auth::user();

        if ($currentUser->id === $targetUser->id) {
            return response()->json(['error' => 'You cannot follow yourself'], 400);
        }

        $existingLike = Like::where('uid', $currentUser->id)
            ->where('sid', $targetUser->id)
            ->where('type', 1) // 1 typically means "Follow" in this system
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            return response()->json(['message' => 'Unfollowed successfully', 'following' => false]);
        } else {
            $like = new Like();
            $like->uid = $currentUser->id;
            $like->sid = $targetUser->id;
            $like->type = 1;
            $like->date = time();
            $like->save();
            return response()->json(['message' => 'Followed successfully', 'following' => true]);
        }
    }

    public function block(Request $request, $identifier)
    {
        $request->validate([
            'block_type' => 'required|in:messages_only,full_platform',
            'duration' => 'nullable|integer|min:1',
        ]);

        $targetUser = User::resolvePublicIdentifier($identifier) ?: User::where('username', $identifier)->first();

        if (!$targetUser) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $currentUser = Auth::user();

        if ($currentUser->id === $targetUser->id) {
            return response()->json(['error' => 'You cannot block yourself'], 400);
        }

        app(\App\Services\UserBlockService::class)->blockUser(
            $currentUser->id,
            $targetUser->id,
            $request->block_type,
            $request->duration
        );

        return response()->json(['message' => 'User blocked successfully', 'blocked' => true]);
    }

    public function unblock($identifier)
    {
        $targetUser = User::resolvePublicIdentifier($identifier) ?: User::where('username', $identifier)->first();

        if (!$targetUser) {
            return response()->json(['error' => 'User not found'], 404);
        }

        app(\App\Services\UserBlockService::class)->unblockUser(Auth::id(), $targetUser->id);

        return response()->json(['message' => 'User unblocked successfully', 'blocked' => false]);
    }

    /**
     * Report a user profile to moderation suite.
     */
    public function report(Request $request, $identifier, \App\Services\ModerationService $moderation)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
            'category' => 'nullable|string|in:spam,harassment,inappropriate,copyright,misinformation,scam,other',
        ]);

        $targetUser = User::resolvePublicIdentifier($identifier) ?: User::where('username', $identifier)->first();
        if (!$targetUser) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $userId = Auth::id();
        if ((int) $userId === (int) $targetUser->id) {
            return response()->json(['error' => 'You cannot report yourself'], 400);
        }

        $sType = 99;
        $tpId = (int) $targetUser->id;

        $existing = \App\Models\Report::where('uid', $userId)
            ->where('s_type', $sType)
            ->where('tp_id', $tpId)
            ->where('statu', 1)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => __('messages.report_already_submitted') ?? 'You have already submitted an active report for this member.',
            ], 422);
        }

        $report = new \App\Models\Report();
        $report->uid = $userId;
        $report->s_type = $sType;
        $report->tp_id = $tpId;
        $report->txt = $request->input('reason');
        $report->category = $request->input('category', 'other');
        $report->statu = 1;
        $report->save();

        $moderation->checkAutoQuarantine($sType, $tpId);

        return response()->json([
            'status' => 'success',
            'message' => __('messages.report_submitted_successfully') ?? 'Your report has been submitted and is under review.',
            'report_id' => $report->id,
        ], 200);
    }
}
