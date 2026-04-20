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
        'txt',
        'cat',
        'group_id',
        'statu',
        'date',
        'reply',
        'vu',
        'is_pinned',
        'pinned_at',
        'pinned_by',
        'is_locked',
        'locked_at',
        'locked_by',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'group_id' => 'integer',
        'pinned_at' => 'integer',
        'locked_at' => 'integer',
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
}
