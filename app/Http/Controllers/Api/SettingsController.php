<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BadgeShowcase;
use App\Models\Option;
use App\Models\PointTransaction;
use App\Models\SecurityMemberSession;
use App\Models\UserBadge;
use App\Models\UserNotificationSetting;
use App\Services\GamificationService;
use App\Services\SecuritySessionService;
use App\Services\SocialValidationService;
use App\Services\UserPrivacyService;
use App\Services\V420SchemaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    public function overview()
    {
        $user = Auth::user();
        return response()->json([
            'user' => [
                'id' => $user->usesPublicMemberIds()
                    ? $user->publicRouteIdentifier()
                    : $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'pts' => $user->pts,
                'avatar' => $user->img,
                'is_verified' => $user->verified,
            ]
        ]);
    }

    public function getProfile()
    {
        $user = Auth::user();
        $privacy = app(UserPrivacyService::class)->settingsFor($user);
        return response()->json([
            'email' => $user->email,
            'about_me' => $user->sig,
            'about_visibility' => $privacy->about_visibility ?? 'public',
            'avatar' => $user->img,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $email = $request->input('email', $user->email);
        $request->merge(['email' => $email]);

        $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
            'avatar' => 'nullable|image|max:2048',
            'cover' => 'nullable|image|max:4096',
            'about_me' => 'nullable|string|max:4000',
            'about_visibility' => 'nullable|in:public,followers,private',
        ]);

        $user->email = $request->email;
        $user->sig = $request->input('about_me', $user->sig);

        if ($request->filled('about_visibility')) {
            app(UserPrivacyService::class)->updateSettings($user, [
                'about_visibility' => $request->about_visibility,
            ]);
        }

        if ($request->filled('password')) {
            $user->pass = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->img = asset('storage/' . $path);
        }

        $user->save();

        if ($request->hasFile('cover')) {
            $coverOption = Option::firstOrCreate(
                ['o_type' => 'user', 'o_order' => $user->id],
                ['name' => $user->username, 'o_parent' => 0]
            );
            $path = $request->file('cover')->store('covers', 'public');
            $coverOption->o_mode = 'storage/' . $path;
            $coverOption->save();
        }

        return response()->json(['message' => 'Profile updated successfully']);
    }

    public function getPrivacy()
    {
        $user = Auth::user();
        $settings = app(UserPrivacyService::class)->settingsFor($user);

        // Map textual settings to numeric codes for mobile app convenience
        $visibilityMap = ['public' => 0, 'followers' => 2, 'private' => 3];
        $visibility = $visibilityMap[$settings->profile_visibility ?? 'public'] ?? 0;
        $dm = $settings->allow_direct_messages ? 0 : 2;
        $mention = $settings->allow_mentions ? 0 : 2;

        return response()->json([
            'settings' => $settings,
            'visibility' => $visibility,
            'dm' => $dm,
            'mention' => $mention,
        ]);
    }

    public function updatePrivacy(Request $request)
    {
        $schema = app(V420SchemaService::class);
        if (!$schema->supports('privacy')) {
            return response()->json(['error' => 'Privacy feature not supported'], 400);
        }

        // Support mobile app shorthand integers (visibility, dm, mention)
        if ($request->has('visibility') || $request->has('dm') || $request->has('mention')) {
            $visInt = (int) $request->input('visibility', 0);
            $dmInt = (int) $request->input('dm', 0);
            $mentionInt = (int) $request->input('mention', 0);

            $profileVis = match ($visInt) {
                1, 2 => 'followers',
                3 => 'private',
                default => 'public',
            };

            $dmAllowed = ($dmInt !== 2);
            $mentionAllowed = ($mentionInt !== 2);

            $merged = [
                'profile_visibility' => $profileVis,
                'about_visibility' => $profileVis,
                'photos_visibility' => $profileVis,
                'followers_visibility' => $profileVis,
                'following_visibility' => $profileVis,
                'points_history_visibility' => 'private',
                'allow_direct_messages' => $dmAllowed,
                'allow_mentions' => $mentionAllowed,
                'allow_reposts' => true,
                'show_online_status' => true,
            ];

            foreach ($merged as $k => $v) {
                if (!$request->has($k)) {
                    $request->merge([$k => $v]);
                }
            }
        }

        $request->validate([
            'profile_visibility' => 'required|in:public,followers,private',
            'about_visibility' => 'required|in:public,followers,private',
            'photos_visibility' => 'required|in:public,followers,private',
            'followers_visibility' => 'required|in:public,followers,private',
            'following_visibility' => 'required|in:public,followers,private',
            'points_history_visibility' => 'required|in:public,followers,private',
            'allow_direct_messages' => 'nullable|boolean',
            'allow_mentions' => 'nullable|boolean',
            'allow_reposts' => 'nullable|boolean',
            'show_online_status' => 'nullable|boolean',
        ]);

        app(UserPrivacyService::class)->updateSettings(Auth::user(), [
            'profile_visibility' => $request->input('profile_visibility'),
            'about_visibility' => $request->input('about_visibility'),
            'photos_visibility' => $request->input('photos_visibility'),
            'followers_visibility' => $request->input('followers_visibility'),
            'following_visibility' => $request->input('following_visibility'),
            'points_history_visibility' => $request->input('points_history_visibility'),
            'allow_direct_messages' => $request->boolean('allow_direct_messages'),
            'allow_mentions' => $request->boolean('allow_mentions'),
            'allow_reposts' => $request->boolean('allow_reposts'),
            'show_online_status' => $request->boolean('show_online_status'),
        ]);

        return response()->json(['message' => 'Privacy settings updated']);
    }

    public function enableTwoFactor(Request $request)
    {
        $user = Auth::user();
        if ($user->hasTwoFactorEnabled()) {
            return response()->json(['error' => '2FA already enabled'], 400);
        }
        $recoveryCodes = [];
        for ($i = 0; $i < 8; $i++) {
            $recoveryCodes[] = Str::upper(Str::random(10));
        }
        $user->two_factor_secret = encrypt(Str::random(32));
        $user->two_factor_recovery_codes = json_encode($recoveryCodes);
        $user->two_factor_type = 'email';
        $user->two_factor_confirmed_at = now();
        $user->save();
        return response()->json(['message' => '2FA enabled', 'recovery_codes' => $recoveryCodes]);
    }

    public function disableTwoFactor(Request $request)
    {
        $user = Auth::user();
        $user->two_factor_secret = null;
        $user->two_factor_recovery_codes = null;
        $user->two_factor_confirmed_at = null;
        $user->save();
        return response()->json(['message' => '2FA disabled']);
    }

    public function getSocial()
    {
        $user = Auth::user();
        $option = Option::where('o_type', 'user_social_links')->where('o_parent', $user->id)->first();
        $links = $option ? (json_decode($option->o_valuer, true) ?: []) : [];

        $socials = [];
        foreach ($links as $platform => $url) {
            $socials[] = [
                'platform' => $platform,
                'url' => $url,
            ];
        }

        return response()->json([
            'links' => $links,
            'socials' => $socials,
        ]);
    }

    public function updateSocial(Request $request, SocialValidationService $socialService)
    {
        $user = Auth::user();
        $platforms = $socialService->getSupportedPlatforms();
        $links = [];

        // Support mobile app payload format {'socials': {'facebook': '...'}}
        $socialsInput = $request->input('socials');
        if (is_array($socialsInput)) {
            $request->merge($socialsInput);
        }

        foreach ($platforms as $platform) {
            $value = trim((string) $request->input($platform));
            if ($value !== '') {
                $normalized = $socialService->normalizeSocialLink($platform, $value);
                if (!$normalized) {
                    return response()->json(['error' => "Invalid URL for $platform"], 422);
                }
                $links[$platform] = $normalized;
            }
        }

        Option::updateOrCreate(
            ['o_type' => 'user_social_links', 'o_parent' => $user->id],
            ['o_valuer' => json_encode($links), 'name' => $user->username, 'o_order' => $user->id]
        );

        $socials = [];
        foreach ($links as $platform => $url) {
            $socials[] = [
                'platform' => $platform,
                'url' => $url,
            ];
        }

        return response()->json([
            'message' => 'Social links updated',
            'links' => $links,
            'socials' => $socials,
        ]);
    }

    public function getNotificationPreferences()
    {
        $user = Auth::user();
        $settings = $user->notificationSetting ?? new UserNotificationSetting();

        $mentionVal = (int) ($settings->email_mention ?? 1);
        $messageVal = (int) ($settings->email_new_message ?? 1);
        $followerVal = (int) ($settings->email_new_follower ?? 1);
        $commentVal = (int) ($settings->email_new_comment ?? 1);

        return response()->json([
            'settings' => $settings,
            // Mobile app shorthand keys
            'email_mentions' => $mentionVal,
            'email_messages' => $messageVal,
            'email_follows' => $followerVal,
            'email_comments' => $commentVal,
            // Canonical schema keys
            'email_mention' => (bool) $mentionVal,
            'email_new_message' => (bool) $messageVal,
            'email_new_follower' => (bool) $followerVal,
            'email_new_comment' => (bool) $commentVal,
        ]);
    }

    public function updateNotificationPreferences(Request $request)
    {
        $user = Auth::user();

        // Support mobile app shorthand parameter aliases
        $aliasMap = [
            'email_mentions' => 'email_mention',
            'email_messages' => 'email_new_message',
            'email_follows' => 'email_new_follower',
            'email_comments' => 'email_new_comment',
        ];
        foreach ($aliasMap as $mobileKey => $canonicalKey) {
            if ($request->has($mobileKey) && !$request->has($canonicalKey)) {
                $request->merge([$canonicalKey => $request->boolean($mobileKey)]);
            }
        }

        $fields = [
            'email_new_follower', 'email_new_comment', 'email_new_message', 
            'email_mention', 'email_repost', 'email_reaction', 
            'email_forum_reply', 'email_marketplace_update'
        ];

        $data = [];
        foreach ($fields as $field) {
            if ($request->has($field)) {
                $data[$field] = $request->boolean($field);
            }
        }

        $user->notificationSetting()->updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return response()->json(['message' => 'Notification preferences updated']);
    }

    public function getSessions(Request $request)
    {
        $user = Auth::user();
        $schema = app(V420SchemaService::class);

        $sessions = [];
        if ($schema->supports('security_sessions')) {
            $sessions = SecurityMemberSession::query()
                ->where('user_id', $user->id)
                ->orderByDesc('last_seen_at')
                ->get();
        }

        $tokens = $user->tokens()
            ->select(['id', 'name', 'abilities', 'last_used_at', 'created_at'])
            ->orderByDesc('last_used_at')
            ->get();

        return response()->json([
            'sessions' => $sessions,
            'sanctum_tokens' => $tokens,
        ]);
    }

    public function revokeSession(Request $request, int $id)
    {
        $user = Auth::user();
        $session = SecurityMemberSession::query()
            ->where('user_id', $user->id)
            ->findOrFail($id);

        app(SecuritySessionService::class)->revoke($session, $user);
        return response()->json(['message' => 'Session revoked']);
    }

    public function revokeToken(Request $request, int $id)
    {
        $user = Auth::user();
        $user->tokens()->where('id', $id)->delete();
        return response()->json(['message' => __('messages.device_revoked_successfully')]);
    }


    public function getBadges()
    {
        $user = Auth::user();
        $earnedBadges = UserBadge::with('badge')
            ->where('user_id', $user->id)
            ->whereNotNull('unlocked_at')
            ->get();

        $showcase = BadgeShowcase::with('badge')
            ->where('user_id', $user->id)
            ->orderBy('sort_order')
            ->get();

        $showcaseBadgeIds = $showcase->pluck('badge_id')->all();

        $badges = $earnedBadges->map(function ($ub) use ($showcaseBadgeIds) {
            $badge = $ub->badge;
            return [
                'id' => $ub->badge_id,
                'name' => $badge?->name ?? ('Badge #' . $ub->badge_id),
                'description' => $badge?->description ?? '',
                'icon' => $badge?->icon_url ?? null,
                'is_shown' => in_array($ub->badge_id, $showcaseBadgeIds, true),
            ];
        })->values();

        return response()->json([
            'earned' => $earnedBadges,
            'showcase' => $showcase,
            'badges' => $badges,
        ]);
    }

    public function updateBadges(Request $request)
    {
        $user = Auth::user();
        $rawInput = $request->input('badge_ids', $request->input('showcase', []));
        $badgeIds = collect($rawInput)
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->take(6)
            ->values();

        $ownedBadgeIds = UserBadge::where('user_id', $user->id)
            ->whereNotNull('unlocked_at')
            ->whereIn('badge_id', $badgeIds)
            ->pluck('badge_id')
            ->all();

        BadgeShowcase::where('user_id', $user->id)->delete();
        foreach (array_values($ownedBadgeIds) as $index => $badgeId) {
            BadgeShowcase::create([
                'user_id' => $user->id,
                'badge_id' => $badgeId,
                'sort_order' => $index + 1,
            ]);
        }

        return response()->json(['message' => 'Badge showcase updated']);
    }

    public function getHistory()
    {
        $user = Auth::user();
        $schema = app(V420SchemaService::class);
        $orderColumn = $schema->hasColumn('point_transactions', 'created_at') ? 'created_at' : 'id';
        try {
            $history = PointTransaction::where('user_id', $user->id)
                ->orderByDesc($orderColumn)
                ->paginate(20);
            return response()->json($history);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed to load API point history: ' . $e->getMessage());
            return response()->json(new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20));
        }
    }

    public function getApps()
    {
        // For OAuth apps if implemented in this project
        // E.g., Laravel Passport or similar. If not, returning empty for now.
        return response()->json(['apps' => []]);
    }

    public function revokeApp(int $id)
    {
        // Revoke app placeholder
        return response()->json(['message' => 'App revoked (not fully implemented yet)']);
    }

    public function getBlocks()
    {
        $user = Auth::user();
        $blocks = \App\Models\UserBlock::with('blockedUser:id,name,username,img')
            ->where('user_id', $user->id)
            ->active()
            ->get();

        return response()->json(['blocks' => $blocks]);
    }

    public function updateDeviceToken(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string', 'max:500'],
        ]);

        $user = Auth::user();
        
        \App\Models\Option::updateOrCreate(
            ['o_type' => 'fcm_token', 'o_parent' => $user->id],
            ['o_valuer' => $request->input('token'), 'name' => 'FCM Device Token', 'o_order' => 0]
        );

        return response()->json(['success' => true, 'message' => 'Device token updated']);
    }
}
