<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DeveloperOAuthService;
use App\Models\User;
use App\Models\Status;
use App\Models\Message;
use App\Services\UserPrivacyService;

class DeveloperApiController extends Controller
{
    protected DeveloperOAuthService $oauthService;

    public function __construct(DeveloperOAuthService $oauthService)
    {
        $this->oauthService = $oauthService;
    }

    protected function validateToken(Request $request, string $requiredScope)
    {
        $header = $request->header('Authorization');
        if (!$header || !str_starts_with($header, 'Bearer ')) {
            return ['error' => 'Missing or invalid Authorization header', 'code' => 401];
        }

        $tokenStr = substr($header, 7);
        $token = $this->oauthService->verifyAccessToken($tokenStr);

        if (!$token) {
            return ['error' => 'Invalid or expired token', 'code' => 401];
        }

        if (!in_array($requiredScope, $token->scopes)) {
            return ['error' => 'Insufficient scope', 'code' => 403];
        }

        return ['token' => $token];
    }

    protected function successResponse($data, $message = 'Success')
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    protected function errorResponse($message, $code)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null
        ], $code);
    }

    public function me(Request $request)
    {
        $auth = $this->validateToken($request, 'user.identity.read');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        $user = $auth['token']->user;

        return $this->successResponse([
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
        ]);
    }

    public function myProfile(Request $request)
    {
        $auth = $this->validateToken($request, 'user.profile.read');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        $user = $auth['token']->user;

        return $this->successResponse([
            'id' => $user->id,
            'username' => $user->username,
            'avatar' => asset($user->img),
            'points' => $user->pts,
        ]);
    }

    public function ownerProfile(Request $request)
    {
        $auth = $this->validateToken($request, 'owner.profile.read');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        $owner = $auth['token']->app->user;

        return $this->successResponse([
            'id' => $owner->id,
            'username' => $owner->username,
            'avatar' => asset($owner->img),
        ]);
    }

    public function ownerContent(Request $request)
    {
        $auth = $this->validateToken($request, 'owner.content.read');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        $owner = $auth['token']->app->user;

        // Only public content
        $posts = Status::where('user_id', $owner->id)
            ->where('privacy', 'public')
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get();

        $data = $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'content' => $post->content,
                'created_at' => $post->created_at,
            ];
        });

        return $this->successResponse($data);
    }

    public function ownerFollow(Request $request)
    {
        $auth = $this->validateToken($request, 'owner.follow.write');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        $user = $auth['token']->user;
        $owner = $auth['token']->app->user;

        if ($user->id === $owner->id) {
            return $this->errorResponse('Cannot follow yourself', 400);
        }

        $existing = \App\Models\Like::where('user1', $user->id)->where('user2', $owner->id)->first();
        if (!$existing) {
            \App\Models\Like::create([
                'user1' => $user->id,
                'user2' => $owner->id,
            ]);
            // Increment follower counts etc if needed
            // This is a simplified version, should ideally use the existing follow logic if decoupled
        }

        return $this->successResponse(null, 'Followed successfully');
    }

    public function ownerMessages(Request $request)
    {
        $auth = $this->validateToken($request, 'owner.messages.write');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        $request->validate(['content' => 'required|string']);

        $user = $auth['token']->user;
        $owner = $auth['token']->app->user;

        if ($user->id === $owner->id) {
            return $this->errorResponse('Cannot message yourself', 400);
        }

        $privacyService = app(UserPrivacyService::class);
        if (!$privacyService->canDirectMessage($user, $owner)) {
            return $this->errorResponse('Messaging restricted by privacy settings', 403);
        }

        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $owner->id,
            'content' => $request->content,
            'seen' => 0,
        ]);

        return $this->successResponse(['id' => $message->id], 'Message sent successfully');
    }
}
