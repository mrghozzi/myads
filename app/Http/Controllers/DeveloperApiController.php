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
            'id' => $user->usesPublicMemberIds()
                ? $user->publicRouteIdentifier()
                : $user->id,
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
            'id' => $user->usesPublicMemberIds()
                ? $user->publicRouteIdentifier()
                : $user->id,
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
            'id' => $owner->usesPublicMemberIds()
                ? $owner->publicRouteIdentifier()
                : $owner->id,
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

        $existing = \App\Models\Like::where('uid', $user->id)
            ->where('sid', $owner->id)
            ->where('type', 1)
            ->first();
            
        if (!$existing) {
            \App\Models\Like::create([
                'uid' => $user->id,
                'sid' => $owner->id,
                'type' => 1,
                'time_t' => time()
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

    public function myEmail(Request $request)
    {
        $auth = $this->validateToken($request, 'user.email.read');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        $user = $auth['token']->user;

        return $this->successResponse([
            'email' => $user->email,
            'email_verified' => !empty($user->email_verified_at),
        ]);
    }

    public function mySocialLinks(Request $request)
    {
        $auth = $this->validateToken($request, 'user.social_links.read');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        $user = $auth['token']->user;

        return $this->successResponse([
            'facebook' => $user->fb ?? null,
            'twitter' => $user->tw ?? null,
            'instagram' => $user->insta ?? null,
            'youtube' => $user->yt ?? null,
            'website' => $user->web ?? null,
        ]);
    }

    public function myFollows(Request $request)
    {
        $auth = $this->validateToken($request, 'user.follows.read');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        $user = $auth['token']->user;

        $followingCount = \App\Models\Like::where('uid', $user->id)->where('type', 1)->count();
        $followersCount = \App\Models\Like::where('sid', $user->id)->where('type', 1)->count();

        return $this->successResponse([
            'following_count' => $followingCount,
            'followers_count' => $followersCount,
        ]);
    }

    public function followUser(Request $request)
    {
        $auth = $this->validateToken($request, 'user.follows.write');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        $request->validate([
            'target_user_id' => 'required|integer',
            'action' => 'nullable|in:follow,unfollow,toggle',
        ]);

        $user = $auth['token']->user;
        $targetId = (int) $request->target_user_id;

        if ($user->id === $targetId) {
            return $this->errorResponse('Cannot follow yourself', 400);
        }

        $targetUser = User::find($targetId);
        if (!$targetUser) {
            return $this->errorResponse('Target user not found', 404);
        }

        $action = $request->action ?? 'toggle';
        $existing = \App\Models\Like::where('uid', $user->id)->where('sid', $targetId)->where('type', 1)->first();

        if ($action === 'unfollow' || ($action === 'toggle' && $existing)) {
            if ($existing) {
                $existing->delete();
            }
            return $this->successResponse(['following' => false], 'Unfollowed successfully');
        } else {
            if (!$existing) {
                \App\Models\Like::create([
                    'uid' => $user->id,
                    'sid' => $targetId,
                    'type' => 1,
                    'time_t' => time(),
                ]);
            }
            return $this->successResponse(['following' => true], 'Followed successfully');
        }
    }

    public function myContent(Request $request)
    {
        $auth = $this->validateToken($request, 'user.content.read');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        $user = $auth['token']->user;

        $posts = Status::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->limit(25)
            ->get();

        $data = $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'content' => $post->content,
                'privacy' => $post->privacy,
                'likes_count' => $post->likes_count ?? 0,
                'comments_count' => $post->comments_count ?? 0,
                'created_at' => $post->created_at,
            ];
        });

        return $this->successResponse($data);
    }

    public function postContent(Request $request)
    {
        $auth = $this->validateToken($request, 'user.content.write');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        $request->validate([
            'content' => 'required|string|max:5000',
            'privacy' => 'nullable|in:public,followers,private',
        ]);

        $user = $auth['token']->user;

        $status = Status::create([
            'user_id' => $user->id,
            'content' => $request->content,
            'privacy' => $request->privacy ?? 'public',
        ]);

        return $this->successResponse(['id' => $status->id], 'Post created successfully');
    }

    public function reactContent(Request $request)
    {
        $auth = $this->validateToken($request, 'user.reactions.write');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        $request->validate([
            'status_id' => 'required|integer',
        ]);

        $user = $auth['token']->user;
        $status = Status::find($request->status_id);
        if (!$status) {
            return $this->errorResponse('Status not found', 404);
        }

        $existing = \App\Models\Like::where('uid', $user->id)->where('sid', $status->id)->where('type', 0)->first();
        if ($existing) {
            $existing->delete();
            return $this->successResponse(['reacted' => false], 'Reaction removed');
        } else {
            \App\Models\Like::create([
                'uid' => $user->id,
                'sid' => $status->id,
                'type' => 0,
                'time_t' => time(),
            ]);
            return $this->successResponse(['reacted' => true], 'Reaction added');
        }
    }

    public function myMessages(Request $request)
    {
        $auth = $this->validateToken($request, 'user.messages.read');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        $user = $auth['token']->user;

        $messages = Message::where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)->orWhere('receiver_id', $user->id);
            })
            ->orderBy('id', 'desc')
            ->limit(30)
            ->get();

        $data = $messages->map(function ($msg) use ($user) {
            return [
                'id' => $msg->id,
                'is_sender' => $msg->sender_id === $user->id,
                'content' => $msg->content,
                'seen' => (bool) $msg->seen,
                'created_at' => $msg->created_at,
            ];
        });

        return $this->successResponse($data);
    }

    public function sendMessage(Request $request)
    {
        $auth = $this->validateToken($request, 'user.messages.write');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        $request->validate([
            'receiver_id' => 'required|integer',
            'content' => 'required|string|max:2000',
        ]);

        $user = $auth['token']->user;
        $receiver = User::find($request->receiver_id);
        if (!$receiver) {
            return $this->errorResponse('Receiver not found', 404);
        }

        if ($user->id === $receiver->id) {
            return $this->errorResponse('Cannot message yourself', 400);
        }

        $privacyService = app(UserPrivacyService::class);
        if (!$privacyService->canDirectMessage($user, $receiver)) {
            return $this->errorResponse('Messaging restricted by privacy settings', 403);
        }

        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiver->id,
            'content' => $request->content,
            'seen' => 0,
        ]);

        return $this->successResponse(['id' => $message->id], 'Message sent successfully');
    }

    public function myNotifications(Request $request)
    {
        $auth = $this->validateToken($request, 'user.notifications.read');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        $user = $auth['token']->user;

        $notifications = \App\Models\Notification::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get();

        $unreadCount = \App\Models\Notification::where('user_id', $user->id)->where('seen', 0)->count();

        return $this->successResponse([
            'unread_count' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    public function myWallet(Request $request)
    {
        $auth = $this->validateToken($request, 'user.wallet.read');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        $user = $auth['token']->user;

        return $this->successResponse([
            'points' => $user->pts,
        ]);
    }

    public function myBadges(Request $request)
    {
        $auth = $this->validateToken($request, 'user.badges.read');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        $user = $auth['token']->user;
        $badges = \App\Models\UserBadge::where('user_id', $user->id)->with('badge')->get();

        return $this->successResponse($badges);
    }

    public function myClips(Request $request)
    {
        $auth = $this->validateToken($request, 'user.clips.read');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        $clips = \App\Models\Short::orderBy('id', 'desc')->limit(20)->get();

        return $this->successResponse($clips);
    }

    public function forumsList(Request $request)
    {
        $auth = $this->validateToken($request, 'user.forums.read');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        $categories = \App\Models\ForumCategory::withCount('topics')->get();

        return $this->successResponse($categories);
    }

    public function storeProducts(Request $request)
    {
        $auth = $this->validateToken($request, 'user.store.read');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        $products = \App\Models\Product::orderBy('id', 'desc')->limit(20)->get();

        return $this->successResponse($products);
    }

    public function myOrders(Request $request)
    {
        $auth = $this->validateToken($request, 'user.orders.read');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        $user = $auth['token']->user;
        $orders = \App\Models\OrderRequest::where('user_id', $user->id)->orderBy('id', 'desc')->limit(20)->get();

        return $this->successResponse($orders);
    }

    public function adsStats(Request $request)
    {
        $auth = $this->validateToken($request, 'user.ads.read');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        $user = $auth['token']->user;
        $bannerImpressions = \App\Models\BannerImpression::where('user_id', $user->id)->count();

        return $this->successResponse([
            'banner_impressions' => $bannerImpressions,
        ]);
    }
}
