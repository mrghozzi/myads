<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

use App\Services\GroupAccessService;
use App\Services\V420SchemaService;
use App\Traits\HasPrivacy;

class ForumTopic extends Model
{
    use HasFactory, HasPrivacy {
        scopeVisible as scopePrivacyVisible;
    }

    protected $table = 'forum';
    public $timestamps = false;

    protected $fillable = [
        'uid',
        'name',
        'slug',
        'txt',
        'cat',
        'group_id',
        'statu',
        'date',
        'reply',
        'comments_count',
        'vu',
        'is_pinned',
        'pinned_at',
        'pinned_by',
        'is_locked',
        'locked_at',
        'locked_by',
        'last_posted_at',
        'last_poster_id',
        'best_comment_id',
        'pinned_comment_id',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'group_id' => 'integer',
        'pinned_at' => 'integer',
        'locked_at' => 'integer',
        'last_posted_at' => 'integer',
        'comments_count' => 'integer',
    ];

    public function scopeVisible(Builder $query, ?User $viewer = null, ?string $column = null): Builder
    {
        $viewer = $viewer ?? Auth::user();

        if ($viewer && $viewer->isAdmin()) {
            return $query;
        }

        $schema = app(V420SchemaService::class);
        $groupAccess = app(GroupAccessService::class);
        $hasGroupColumn = $schema->hasColumn($this->getTable(), 'group_id');
        $visibleGroupIds = $hasGroupColumn && $groupAccess->featureEnabled()
            ? $groupAccess->visibleGroupIdsFor($viewer)
            : [];

        $query->where(function (Builder $visibilityQuery) use ($viewer, $column, $hasGroupColumn, $visibleGroupIds) {
            $visibilityQuery->where(function (Builder $ungroupedQuery) use ($viewer, $column, $hasGroupColumn) {
                if ($hasGroupColumn) {
                    $ungroupedQuery->whereNull('group_id');
                }

                $ungroupedQuery->privacyVisible($viewer, $column);
            });

            if ($visibleGroupIds !== []) {
                $visibilityQuery->orWhereIn('group_id', $visibleGroupIds);
            }
        });

        return $query;
    }

    public function scopeForumOnly(Builder $query): Builder
    {
        return $query->whereExists(function ($sub) {
            $sub->select(\Illuminate\Support\Facades\DB::raw(1))
                ->from('status')
                ->whereColumn('status.tp_id', 'forum.id')
                ->where('status.s_type', 2);
        })->where('forum.cat', '>', 0);
    }

    public function status()
    {
        return $this->hasOne(Status::class, 'tp_id', 'id')->where('s_type', 2);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }

    public function category()
    {
        return $this->belongsTo(ForumCategory::class, 'cat');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function comments()
    {
        return $this->hasMany(ForumComment::class, 'tid');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'sid')->where('type', 2);
    }

    public function imageOption()
    {
        return $this->hasOne(Option::class, 'o_parent', 'id')->where('o_type', 'image_post');
    }

    public function getImageUrlAttribute()
    {
        return $this->imageOption ? $this->imageOption->o_valuer : null;
    }

    public function pinnedBy()
    {
        return $this->belongsTo(User::class, 'pinned_by');
    }

    public function lockedBy()
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function attachments()
    {
        return $this->hasMany(ForumAttachment::class, 'topic_id')->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
    }

    public function lastPoster()
    {
        return $this->belongsTo(User::class, 'last_poster_id');
    }

    public function lastComment()
    {
        return $this->hasOne(ForumComment::class, 'tid')->latest('id');
    }

    public function bestComment()
    {
        return $this->belongsTo(ForumComment::class, 'best_comment_id');
    }

    public function pinnedComment()
    {
        return $this->belongsTo(ForumComment::class, 'pinned_comment_id');
    }

    public function scopeFilterModern(Builder $query, string $filter = 'latest'): Builder
    {
        return match ($filter) {
            'top' => $query->orderByDesc('comments_count')->orderByDesc('date'),
            'newest' => $query->orderByDesc('date')->orderByDesc('id'),
            'unanswered' => $query->where('comments_count', 0)->orderByDesc('date'),
            default => $query->orderByDesc('last_posted_at')->orderByDesc('id'),
        };
    }

    public function supportsPromotion(): bool
    {
        // Topic must be public (not in a group) to be promoted in the global feed
        return (int) ($this->group_id ?? 0) === 0;
    }
}
