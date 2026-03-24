<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\Directory;
use App\Models\ForumComment;
use App\Models\ForumTopic;
use App\Models\Like;
use App\Models\News;
use App\Models\Option;
use App\Models\Product;
use App\Models\StatusLinkPreview;
use App\Models\StatusRepost;
use App\Services\V420SchemaService;

use App\Traits\HasPrivacy;

class Status extends Model
{
    use HasFactory, HasPrivacy;

    protected $table = 'status';
    public $timestamps = false;

    protected $fillable = [
        'uid',
        'tp_id',
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

    public function linkPreviewRecord()
    {
        return $this->hasOne(StatusLinkPreview::class, 'status_id');
    }

    public function repostRecord()
    {
        return $this->hasOne(StatusRepost::class, 'status_id');
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

            return Like::where('sid', $this->tp_id)
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
                ->where('sid', $this->tp_id)
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

        return null;
    }
}
