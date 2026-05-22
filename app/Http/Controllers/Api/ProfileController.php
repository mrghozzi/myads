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
        $user = User::resolvePublicIdentifier($identifier);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return new UserProfileResource($user);
    }

    public function statuses($identifier, Request $request)
    {
        $user = User::resolvePublicIdentifier($identifier);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Basic fetch of user statuses
        $statuses = Status::visible()
            ->where('uid', $user->id)
            ->orderBy('date', 'desc')
            ->paginate(20);

        return StatusResource::collection($statuses);
    }

    public function follow($identifier)
    {
        $targetUser = User::resolvePublicIdentifier($identifier);

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
}
