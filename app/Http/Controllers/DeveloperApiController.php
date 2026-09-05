<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Services\DeveloperOAuthService;
use App\Models\User;
use App\Models\Status;
use App\Models\ForumTopic;
use App\Models\Message;
use App\Services\UserPrivacyService;
use App\Services\DeveloperScopeCatalog;

class DeveloperApiController extends Controller
{
    protected DeveloperOAuthService $oauthService;

    public function __construct(DeveloperOAuthService $oauthService)
    {
        $this->oauthService = $oauthService;
    }

    protected function validateToken(Request $request, string $requiredScope)
    {
        $tokenStr = $request->bearerToken();

        if (!$tokenStr) {
            $header = $request->header('Authorization')
                ?? $request->header('authorization')
                ?? $request->server('HTTP_AUTHORIZATION')
                ?? $request->server('REDIRECT_HTTP_AUTHORIZATION')
                ?? $request->server('REDIRECT_REDIRECT_HTTP_AUTHORIZATION')
                ?? (function_exists('apache_request_headers') ? (apache_request_headers()['Authorization'] ?? apache_request_headers()['authorization'] ?? null) : null);

            if ($header && preg_match('/Bearer\s+(\S+)/i', $header, $matches)) {
                $tokenStr = $matches[1];
            }
        }

        // Fallback to request input/query access_token
        if (!$tokenStr && $request->has('access_token')) {
            $tokenStr = $request->input('access_token');
        }

        if ($tokenStr) {
            $tokenStr = trim($tokenStr);
        }

        if (!$tokenStr) {
            return ['error' => 'Missing or invalid Authorization header', 'code' => 401];
        }

        $token = $this->oauthService->verifyAccessToken($tokenStr);

        if (!$token) {
            return ['error' => 'Invalid or expired token', 'code' => 401];
        }

        $rawScopes = $token->scopes;
        if (is_string($rawScopes)) {
            $decoded = json_decode($rawScopes, true);
            $rawScopes = is_array($decoded) ? $decoded : preg_split('/[\s,]+/', trim($rawScopes));
        }

        $scopes = [];
        if (is_array($rawScopes)) {
            foreach ($rawScopes as $item) {
                if (is_string($item)) {
                    foreach (preg_split('/[\s,]+/', trim($item)) as $sub) {
                        if ($sub !== '') {
                            $scopes[] = DeveloperScopeCatalog::normalizeScopeId($sub);
                        }
                    }
                }
            }
        }

        $requiredScope = DeveloperScopeCatalog::normalizeScopeId($requiredScope);

        $hasScope = in_array($requiredScope, $scopes, true)
            || in_array('*', $scopes, true)
            || in_array('all', $scopes, true);

        if (!$hasScope) {
            return ['error' => 'Insufficient scope', 'code' => 403];
        }

        if (!$token->user) {
            return ['error' => 'Authenticated user not found', 'code' => 404];
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

        try {
            $user = $auth['token']->user;

            return $this->successResponse([
                'id' => $user->usesPublicMemberIds()
                    ? $user->publicRouteIdentifier()
                    : $user->id,
                'user_id' => $user->id,
                'username' => $user->username,
                'name' => $user->username,
                'email' => $user->email,
                'avatar' => $user->img ? asset($user->img) : null,
                'points' => $user->pts ?? 0,
                'verified' => !empty($user->ucheck),
            ]);
        } catch (\Throwable $e) {
            Log::error('Developer API me error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve identity: ' . $e->getMessage(), 500);
        }
    }

    public function myProfile(Request $request)
    {
        $auth = $this->validateToken($request, 'user.profile.read');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        try {
            $user = $auth['token']->user;

            return $this->successResponse([
                'id' => $user->usesPublicMemberIds()
                    ? $user->publicRouteIdentifier()
                    : $user->id,
                'user_id' => $user->id,
                'username' => $user->username,
                'name' => $user->username,
                'email' => $user->email,
                'avatar' => $user->img ? asset($user->img) : null,
                'points' => $user->pts ?? 0,
                'verified' => !empty($user->ucheck),
            ]);
        } catch (\Throwable $e) {
            Log::error('Developer API myProfile error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve profile: ' . $e->getMessage(), 500);
        }
    }

    public function ownerProfile(Request $request)
    {
        $auth = $this->validateToken($request, 'owner.profile.read');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        try {
            $owner = $auth['token']->app?->user;
            if (!$owner) {
                return $this->errorResponse('Application owner not found', 404);
            }

            return $this->successResponse([
                'id' => $owner->usesPublicMemberIds()
                    ? $owner->publicRouteIdentifier()
                    : $owner->id,
                'username' => $owner->username,
                'avatar' => $owner->img ? asset($owner->img) : null,
            ]);
        } catch (\Throwable $e) {
            Log::error('Developer API ownerProfile error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve owner profile: ' . $e->getMessage(), 500);
        }
    }

    public function ownerContent(Request $request)
    {
        $auth = $this->validateToken($request, 'owner.content.read');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        try {
            $owner = $auth['token']->app?->user;
            if (!$owner) {
                return $this->errorResponse('Application owner not found', 404);
            }

            // Only public content
            $posts = Status::visible()
                ->where('uid', $owner->id)
                ->where('statu', 1)
                ->orderBy('id', 'desc')
                ->limit(20)
                ->get();

            $data = $posts->map(function ($post) {
                return [
                    'id' => $post->id,
                    'content' => $post->txt,
                    'created_at' => $post->date ? date('Y-m-d H:i:s', $post->date) : null,
                ];
            });

            return $this->successResponse($data);
        } catch (\Throwable $e) {
            Log::error('Developer API ownerContent error: ' . $e->getMessage());
            return $this->errorResponse('Failed to retrieve content: ' . $e->getMessage(), 500);
        }
    }

    public function ownerFollow(Request $request)
    {
        $auth = $this->validateToken($request, 'owner.follow.write');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        try {
            $user = $auth['token']->user;
            $owner = $auth['token']->app?->user;

            if (!$owner) {
                return $this->errorResponse('Application owner not found', 404);
            }

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
            }

            return $this->successResponse(null, 'Followed successfully');
        } catch (\Throwable $e) {
            Log::error('Developer API ownerFollow error: ' . $e->getMessage());
            return $this->errorResponse('Failed to follow user: ' . $e->getMessage(), 500);
        }
    }

    public function ownerMessages(Request $request)
    {
        $auth = $this->validateToken($request, 'owner.messages.write');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        $request->validate(['content' => 'required|string|max:2000']);

        try {
            $user = $auth['token']->user;
            $owner = $auth['token']->app?->user;

            if (!$owner) {
                return $this->errorResponse('Application owner not found', 404);
            }

            if ($user->id === $owner->id) {
                return $this->errorResponse('Cannot message yourself', 400);
            }

            $privacyService = app(UserPrivacyService::class);
            if (!$privacyService->canDirectMessage($user, $owner)) {
                return $this->errorResponse('Messaging restricted by privacy settings', 403);
            }

            $message = Message::create([
                'us_env' => $user->id,
                'us_rec' => $owner->id,
                'msg' => $request->input('content'),
                'time' => time(),
                'state' => 3,
            ]);

            return $this->successResponse(['id' => $message->id_msg ?? $message->id], 'Message sent successfully');
        } catch (\Throwable $e) {
            Log::error('Developer API ownerMessages error: ' . $e->getMessage());
            return $this->errorResponse('Failed to send message: ' . $e->getMessage(), 500);
        }
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

        $posts = Status::where('uid', $user->id)
            ->where('statu', 1)
            ->orderBy('id', 'desc')
            ->limit(25)
            ->get();

        $data = $posts->map(function ($post) {
            return [
                'id' => $post->id,
                'content' => $post->txt,
                'likes_count' => $post->reactions_count ?? 0,
                'comments_count' => $post->comments_count ?? 0,
                'created_at' => $post->date ? date('Y-m-d H:i:s', $post->date) : null,
            ];
        });

        return $this->successResponse($data);
    }

    public function postContent(Request $request)
    {
        $auth = $this->validateToken($request, 'user.content.write');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        $request->validate([
            'content' => 'required|string|max:10000',
        ]);

        try {
            $user = $auth['token']->user;
            $content = $request->input('content');
            $now = time();

            // Extract topic title or fallback to first line / snippet
            $firstLine = trim((string) strtok($content, "\r\n"));
            $title = Str::limit($firstLine, 120, '...');
            if (empty($title)) {
                $title = 'text';
            }

            $topic = ForumTopic::create([
                'uid' => $user->id,
                'name' => $title,
                'txt' => $content,
                'cat' => 0,
                'group_id' => null,
                'statu' => 1,
                'date' => $now,
                'reply' => 0,
                'vu' => 0,
            ]);

            $status = Status::create([
                'uid' => $user->id,
                'group_id' => null,
                'tp_id' => $topic->id,
                's_type' => 100,
                'txt' => $content,
                'statu' => 1,
                'date' => $now,
            ]);

            return $this->successResponse([
                'id' => $status->id,
                'topic_id' => $topic->id,
                'url' => url('/status/' . $status->id),
            ], 'Post created successfully');
        } catch (\Throwable $e) {
            Log::error('Developer API postContent error: ' . $e->getMessage(), [
                'exception' => $e,
            ]);
            return $this->errorResponse('Failed to create post: ' . $e->getMessage(), 500);
        }
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
                $q->where('us_env', $user->id)->orWhere('us_rec', $user->id);
            })
            ->orderBy('id_msg', 'desc')
            ->limit(30)
            ->get();

        $data = $messages->map(function ($msg) use ($user) {
            return [
                'id' => $msg->id_msg,
                'is_sender' => (int) $msg->us_env === (int) $user->id,
                'content' => $msg->text ?? $msg->msg,
                'seen' => (int) $msg->state === 1,
                'created_at' => $msg->time ? date('Y-m-d H:i:s', $msg->time) : null,
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
            'us_env' => $user->id,
            'us_rec' => $receiver->id,
            'msg' => $request->input('content'),
            'time' => time(),
            'state' => 3,
        ]);

        return $this->successResponse(['id' => $message->id_msg ?? $message->id], 'Message sent successfully');
    }

    public function myNotifications(Request $request)
    {
        $auth = $this->validateToken($request, 'user.notifications.read');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        $user = $auth['token']->user;

        $notifications = \App\Models\Notification::where('uid', $user->id)
            ->orderBy('id', 'desc')
            ->limit(20)
            ->get();

        $unreadCount = \App\Models\Notification::where('uid', $user->id)->whereIn('state', [0, 3])->count();

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

        $clips = Status::visible()->where('s_type', 14)->orderBy('id', 'desc')->limit(20)->get();

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
        $orders = \App\Models\OrderRequest::where('uid', $user->id)->orderBy('id', 'desc')->limit(20)->get();

        return $this->successResponse($orders);
    }

    public function adsStats(Request $request)
    {
        $auth = $this->validateToken($request, 'user.ads.read');
        if (isset($auth['error'])) return $this->errorResponse($auth['error'], $auth['code']);

        $user = $auth['token']->user;
        $bannerImpressions = \App\Models\BannerImpression::where('publisher_id', $user->id)->count();

        return $this->successResponse([
            'banner_impressions' => $bannerImpressions,
        ]);
    }
}
