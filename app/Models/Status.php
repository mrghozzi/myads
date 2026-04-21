<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Directory;
use App\Models\ForumComment;
use App\Models\ForumTopic;
use App\Models\Group;
use App\Models\Like;
use App\Models\News;
use App\Models\Option;
use App\Models\Product;
use App\Models\StatusPromotion;
use App\Models\StatusLinkPreview;
use App\Models\StatusRepost;
use App\Services\GroupAccessService;
use App\Services\V420SchemaService;
use App\Services\KnowledgebaseCommunityService;
use App\Models\OrderRequest;
use App\Models\OrderOffer;

use App\Traits\HasPrivacy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class Status extends Model
{
    use HasFactory;
    use HasPrivacy {
        scopeVisible as scopePrivacyVisible;
    }

    protected $table = 'status';
    public $timestamps = false;

    /**
     * Override scopeVisible to handle suspended product activity.
     */
    public function scopeVisible(Builder $query, ?User $viewer = null, ?string $column = null): Builder
    {
        $viewer = $viewer ?? Auth::user();

        $schema = app(V420SchemaService::class);
        $groupsEnabled = \App\Support\GroupSettings::isEnabled();

        if ($viewer && $viewer->isAdmin()) {
            if (!$groupsEnabled && $schema->hasColumn($this->getTable(), 'group_id')) {
                $query->whereNull('group_id');
            }
            return $query;
        }

        $authorIdColumn = $column ?? $this->getAuthorIdColumn();
        $groupAccess = app(GroupAccessService::class);
        $hasGroupColumn = $schema->hasColumn($this->getTable(), 'group_id');
        $visibleGroupIds = ($hasGroupColumn && $groupsEnabled) ? $groupAccess->visibleGroupIdsFor($viewer) : [];

        $query->where(function (Builder $visibilityQuery) use ($viewer, $column, $authorIdColumn, $hasGroupColumn, $visibleGroupIds, $groupsEnabled) {
            $visibilityQuery->where(function (Builder $ungroupedQuery) use ($viewer, $column, $authorIdColumn, $hasGroupColumn) {
                if ($hasGroupColumn) {
                    $ungroupedQuery->whereNull('group_id');
                }

                $ungroupedQuery->privacyVisible($viewer, $column);

                $ungroupedQuery->where(function ($q) use ($viewer, $authorIdColumn) {
                    $q->where(function ($inner) {
                        $inner->where('s_type', '!=', 7867)
                            ->orWhereDoesntHave('productItem.statusOptions', function ($sub) {
                                $sub->where('name', 'suspended');
                            });
                    });

                    if ($viewer) {
                        $q->orWhere($authorIdColumn, $viewer->id);
                    }
                });
            });

            if ($groupsEnabled && $visibleGroupIds !== []) {
                $visibilityQuery->orWhereIn('group_id', $visibleGroupIds);
            }
        });

        return $query;
    }

    protected $fillable = [
        'uid',
        'tp_id',
        'group_id',
        's_type',
        'date',
        'txt',
        'statu',
    ];

    protected $appends = ['date_formatted', 'reactions_count', 'comments_count', 'grouped_reactions', 'reposts_count', 'post_kind'];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }

    public function forumTopic()
    {
        return $this->belongsTo(ForumTopic::class, 'tp_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function directoryListing()
    {
        return $this->belongsTo(Directory::class, 'tp_id');
    }

    public function newsItem()
    {
        return $this->belongsTo(News::class, 'tp_id');
    }

    public function productItem()
    {
        return $this->belongsTo(Product::class, 'tp_id');
    }

    public function knowledgebaseItem()
    {
        return $this->belongsTo(Option::class, 'tp_id')->where('o_type', 'knowledgebase');
    }

    public function linkPreviewRecord()
    {
        return $this->hasOne(StatusLinkPreview::class, 'status_id');
    }

    public function repostRecord()
    {
        return $this->hasOne(StatusRepost::class, 'status_id');
    }

    public function orderRequest()
    {
        return $this->belongsTo(OrderRequest::class, 'tp_id');
    }

    public function promotions()
    {
        return $this->hasMany(StatusPromotion::class, 'status_id');
    }

    public function activePromotion()
    {
        return $this->hasOne(StatusPromotion::class, 'status_id')
            ->where('status', StatusPromotion::STATUS_ACTIVE);
    }

    public function supportsPromotion(): bool
    {
        return in_array((int) $this->s_type, [1, 2, 4, 100, 7867, 6], true);
    }

    public function promotionDestinationUrl(): string
    {
        return match ((int) $this->s_type) {
            1 => route('directory.show', $this->tp_id),
            2, 4, 100 => route('forum.topic', $this->tp_id),
            7867 => route('store.show', $this->related_content->name ?? optional($this->productItem)->name ?? $this->tp_id),
            6 => route('orders.show', $this->tp_id),
            KnowledgebaseCommunityService::STATUS_TYPE => route('kb.show', [
                'name' => $this->related_content->o_mode ?? $this->tp_id,
                'article' => $this->related_content->name ?? $this->tp_id,
            ]),
            default => route('portal.index'),
        };
    }

    public function getRelatedContentAttribute()
    {
        if ($this->relationLoaded('forumTopic') && $this->forumTopic) {
            return $this->forumTopic;
        }

        if ($this->relationLoaded('directoryListing') && $this->directoryListing) {
            return $this->directoryListing;
        }

        if ($this->relationLoaded('productItem') && $this->productItem) {
            return $this->productItem;
        }

        if ($this->relationLoaded('knowledgebaseItem') && $this->knowledgebaseItem) {
            return $this->knowledgebaseItem;
        }

        if ($this->relationLoaded('newsItem') && $this->newsItem) {
            return $this->newsItem;
        }

        if (in_array($this->s_type, [100, 4, 2])) {
            return ForumTopic::find($this->tp_id);
        } elseif ($this->s_type == 1) {
            return Directory::find($this->tp_id);
        } elseif ($this->s_type == 7867) {
            return Product::find($this->tp_id) ?? ForumTopic::find($this->tp_id);
        } elseif ($this->s_type == 5) {
            return News::find($this->tp_id);
        } elseif ($this->s_type == 6) {
            return OrderRequest::find($this->tp_id);
        } elseif ((int) $this->s_type === KnowledgebaseCommunityService::STATUS_TYPE) {
            return Option::query()
                ->where('id', $this->tp_id)
                ->where('o_type', 'knowledgebase')
                ->first();
        }
        return null;
    }

    public function getDateFormattedAttribute()
    {
        try {
            return Carbon::createFromTimestamp($this->date)->diffForHumans();
        } catch (\Throwable $e) {
            return '';
        }
    }

    public function getReactionsCountAttribute()
    {
        try {
            $type = $this->getReactionType();
            if (!$type) {
                return 0;
            }

            return Like::where('sid', $this->interactionSubjectId())
                ->where('type', $type)
                ->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    public function getCommentsCountAttribute()
    {
        try {
            if ($this->s_type == 1) {
                return Option::where('o_parent', $this->tp_id)
                    ->where('o_type', 'd_coment')
                    ->count();
            }

            if (in_array($this->s_type, [2, 4, 100])) {
                return ForumComment::where('tid', $this->tp_id)->count();
            }

            if ($this->s_type == 7867) {
                return Option::where('o_parent', $this->tp_id)
                    ->where('o_type', 's_coment')
                    ->count();
            }

            if ($this->s_type == 6) {
                return OrderOffer::where('order_request_id', $this->tp_id)
                    ->marketplaceVisible()
                    ->count();
            }

            if ((int) $this->s_type === KnowledgebaseCommunityService::STATUS_TYPE) {
                return Option::where('o_parent', $this->id)
                    ->where('o_type', KnowledgebaseCommunityService::COMMENT_OPTION_TYPE)
                    ->count();
            }

            return 0;
        } catch (\Throwable $e) {
            return 0;
        }
    }

    public function getGroupedReactionsAttribute()
    {
        try {
            $type = $this->getReactionType();
            if (!$type) {
                return [];
            }

            $likes = Like::with('user')
                ->where('sid', $this->interactionSubjectId())
                ->where('type', $type)
                ->get();

            if ($likes->isEmpty()) {
                return [];
            }

            $options = Option::whereIn('o_parent', $likes->pluck('id'))
                ->where('o_type', 'data_reaction')
                ->get()
                ->keyBy('o_parent');

            $grouped = [];
            foreach ($likes as $like) {
                if (!$like->user) {
                    continue;
                }
                $option = $options->get($like->id);
                $reaction = $option ? $option->o_valuer : 'like';
                $grouped[$reaction][] = $like->user;
            }

            return $grouped;
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function getRepostsCountAttribute(): int
    {
        if (!app(V420SchemaService::class)->supports('reposts')) {
            return 0;
        }

        try {
            return StatusRepost::where('original_status_id', $this->id)->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    public function getPostKindAttribute(): string
    {
        $schema = app(V420SchemaService::class);

        if ($schema->supports('reposts')) {
            try {
                $hasRepost = $this->relationLoaded('repostRecord')
                    ? $this->getRelation('repostRecord') !== null
                    : $this->repostRecord()->exists();

                if ($hasRepost) {
                    return 'repost';
                }
            } catch (\Throwable) {
                // Keep falling back to other post kinds.
            }
        }

        if ($schema->supports('link_previews')) {
            try {
                $hasLinkPreview = $this->relationLoaded('linkPreviewRecord')
                    ? $this->getRelation('linkPreviewRecord') !== null
                    : $this->linkPreviewRecord()->exists();

                if ($hasLinkPreview) {
                    return 'link';
                }
            } catch (\Throwable) {
                // Keep falling back to other post kinds.
            }
        }

        if ((int) $this->s_type === 4) {
            return 'gallery';
        }

        return 'text';
    }

    private function getReactionType()
    {
        if ($this->s_type == 1) {
            return 22;
        }

        if (in_array($this->s_type, [2, 4, 100])) {
            return 2;
        }

        if ($this->s_type == 7867) {
            return 3;
        }

        if ($this->s_type == 6) {
            return 6; // Fixed: Use type 6 for Order Requests as per ReactionController
        }

        if ((int) $this->s_type === KnowledgebaseCommunityService::STATUS_TYPE) {
            return KnowledgebaseCommunityService::REACTION_TYPE;
        }

        return null;
    }

    private function interactionSubjectId(): int
    {
        if ((int) $this->s_type === KnowledgebaseCommunityService::STATUS_TYPE) {
            return (int) $this->id;
        }

        return (int) $this->tp_id;
    }
}
