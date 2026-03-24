<?php

namespace App\Traits;

use App\Models\Like;
use App\Models\User;
use App\Models\UserPrivacySetting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait HasPrivacy
{
    /**
     * Scope a query to only include visible content based on author's privacy settings.
     *
     * @param Builder $query
     * @param User|null $viewer
     * @return Builder
     */
    public function scopeVisible(Builder $query, ?User $viewer = null, ?string $column = null): Builder
    {
        $viewer = $viewer ?? Auth::user();
        $authorIdColumn = $column ?? $this->getAuthorIdColumn();

        // 1. If viewer is Admin, they see everything.
        if ($viewer && $viewer->isAdmin()) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($viewer, $authorIdColumn) {
            // 2. Owner can always see their own content.
            if ($viewer) {
                $q->orWhere($authorIdColumn, $viewer->id);
            }

            // 3. Content is visible if author has 'public' profile visibility.
            $q->orWhereIn($authorIdColumn, function ($sub) {
                $sub->select('user_id')
                    ->from('user_privacy_settings')
                    ->where('profile_visibility', 'public');
            });

            // 4. Content is visible if author has 'followers' visibility AND viewer follows author.
            if ($viewer) {
                $followingIds = Like::where('uid', $viewer->id)
                    ->where('type', 1) // Follow type
                    ->pluck('sid');

                if ($followingIds->isNotEmpty()) {
                    $q->orWhere(function (Builder $q2) use ($authorIdColumn, $followingIds) {
                        $q2->whereIn($authorIdColumn, $followingIds)
                           ->whereIn($authorIdColumn, function ($sub) {
                               $sub->select('user_id')
                                   ->from('user_privacy_settings')
                                   ->where('profile_visibility', 'followers');
                           });
                    });
                }
            }
        });
    }

    /**
     * Get the column name for the author ID.
     *
     * @return string
     */
    protected function getAuthorIdColumn(): string
    {
        return property_exists($this, 'authorIdColumn') ? $this->authorIdColumn : 'uid';
    }
}
