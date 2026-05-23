<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Like;
use App\Models\Status;
use Illuminate\Support\Facades\Auth;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $viewer = Auth::guard('sanctum')->user() ?? Auth::user();
        $schema = app(\App\Services\V420SchemaService::class);

        // Resolve cover
        $coverOption = \App\Models\Option::where('o_type', 'user')->where('o_order', $this->id)->first();
        $cover = trim((string) ($coverOption?->o_mode ?? ''));
        if ($cover === '' || $cover === '0') {
            $cover = 'upload/cover.jpg';
        }
        $coverUrl = asset($cover);

        // Follow status
        $isFollowing = $viewer
            ? Like::where('uid', $viewer->id)->where('sid', $this->id)->where('type', 1)->exists()
            : false;

        // Subscription badge
        $subscriptionProfileBadge = app(\App\Services\Billing\SubscriptionEntitlementService::class)->activeProfileBadgeForUserId($this->id);

        // Social links
        $socialOption = \App\Models\Option::where('o_type', 'user_social_links')->where('o_parent', $this->id)->first();
        $socialLinks = $socialOption ? json_decode($socialOption->o_valuer, true) : [];

        // Badges showcase
        $badgeShowcase = collect();
        if ($schema->supports('badges')) {
            $badgeShowcase = \App\Models\BadgeShowcase::with('badge')
                ->where('user_id', $this->id)
                ->orderBy('sort_order')
                ->take(6)
                ->get();
        }
        if ($schema->supports('badges') && $badgeShowcase->isEmpty()) {
            $badgeShowcase = \App\Models\UserBadge::with('badge')
                ->where('user_id', $this->id)
                ->whereNotNull('unlocked_at')
                ->orderByDesc('unlocked_at')
                ->take(6)
                ->get()
                ->map(fn ($item) => (object) ['badge' => $item->badge, 'sort_order' => 0]);
        }

        $badgesData = [];
        foreach ($badgeShowcase as $item) {
            $badge = $item->badge;
            if ($badge) {
                $badgesData[] = [
                    'name' => __('messages.' . $badge->name_key),
                    'icon' => $badge->icon,
                ];
            }
        }

        return [
            'id' => $this->id,
            'username' => $this->username,
            'name' => $this->name,
            'avatar' => $this->avatarUrl(),
            'pts' => $this->pts,
            'verified' => $this->hasVerifiedBadge(),
            'bio' => $this->sig,
            'followers_count' => Like::where('sid', $this->id)->where('type', 1)->count(),
            'following_count' => Like::where('uid', $this->id)->where('type', 1)->count(),
            'posts_count' => Status::where('uid', $this->id)->where('s_type', '!=', 5)->count(),
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'online' => $this->isOnline(),
            'cover' => $coverUrl,
            'is_following' => $isFollowing,
            'subscription_badge' => $subscriptionProfileBadge,
            'social_links' => (object)$socialLinks,
            'badges' => $badgesData,
            'profile_badge_color' => $this->profileBadgeColor(),
        ];
    }
}
