<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PollOption extends Model
{
    use HasFactory;

    protected $table = 'poll_options';

    protected $fillable = [
        'poll_id',
        'option_text',
        'votes_count',
        'order',
    ];

    protected $casts = [
        'votes_count' => 'integer',
        'order' => 'integer',
    ];

    public function poll()
    {
        return $this->belongsTo(Poll::class, 'poll_id');
    }

    public function votes()
    {
        return $this->hasMany(PollVote::class, 'poll_option_id');
    }

    public function getPercentageAttribute(): float
    {
        $total = $this->poll ? $this->poll->total_votes : 0;
        if ($total <= 0) {
            return 0.0;
        }

        return round(($this->votes_count / $total) * 100, 1);
    }
}

