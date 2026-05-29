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
                'id' => $user->id,
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
        return response()->json([
            'email' => $user->email,
            'about_me' => $user->sig,
            'avatar' => $user->img,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
            'avatar' => 'nullable|image|max:2048',
            'cover' => 'nullable|image|max:4096',
            'about_me' => 'nullable|string|max:4000',
        ]);

        $user->email = $request->email;
        $user->sig = $request->input('about_me', $user->sig);

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
        return response()->json(['settings' => $settings]);
    }

    public function updatePrivacy(Request $request)
    {
        $schema = app(V420SchemaService::class);
        if (!$schema->supports('privacy')) {
            return response()->json(['error' => 'Privacy feature not supported'], 400);
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
        $links = $option ? json_decode($option->o_valuer, true) : [];
        return response()->json(['links' => $links]);
    }

    public function updateSocial(Request $request, SocialValidationService $socialService)
    {
        $user = Auth::user();
        $platforms = $socialService->getSupportedPlatforms();
        $links = [];

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

        return response()->json(['message' => 'Social links updated', 'links' => $links]);
    }

    public function getNotificationPreferences()
    {
        $user = Auth::user();
        $settings = $user->notificationSetting ?? new UserNotificationSetting();
        return response()->json(['settings' => $settings]);
    }

    public function updateNotificationPreferences(Request $request)
    {
        $user = Auth::user();
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

        if (!$schema->supports('security_sessions')) {
            return response()->json(['sessions' => []]);
        }

        $sessions = SecurityMemberSession::query()
            ->where('user_id', $user->id)
            ->orderByDesc('last_seen_at')
            ->get();

        return response()->json(['sessions' => $sessions]);
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

        return response()->json(['earned' => $earnedBadges, 'showcase' => $showcase]);
    }

    public function updateBadges(Request $request)
    {
        $user = Auth::user();
        $badgeIds = collect($request->input('badge_ids', []))
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
        $history = PointTransaction::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(20);
        return response()->json($history);
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
}
