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

class Status extends Model
{
    use HasFactory;

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

    protected $appends = ['date_formatted', 'reactions_count', 'comments_count', 'grouped_reactions'];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }

    public function getRelatedContentAttribute()
    {
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
        return Carbon::createFromTimestamp($this->date)->diffForHumans();
    }

    public function getReactionsCountAttribute()
    {
        $type = $this->getReactionType();
        if (!$type) {
            return 0;
        }

        return Like::where('sid', $this->tp_id)
            ->where('type', $type)
            ->count();
    }

    public function getCommentsCountAttribute()
    {
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
    }

    public function getGroupedReactionsAttribute()
    {
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
            $reaction = $options->get($like->id)->o_valuer ?? 'like';
            $grouped[$reaction][] = $like->user;
        }

        return $grouped;
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
