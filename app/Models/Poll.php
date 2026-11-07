<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    use HasFactory;

    protected $table = 'polls';

    protected $fillable = [
        'status_id',
        'user_id',
        'question',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    protected $appends = [
        'total_votes',
        'is_expired',
    ];

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function options()
    {
        return $this->hasMany(PollOption::class, 'poll_id')->orderBy('order', 'asc')->orderBy('id', 'asc');
    }

    public function votes()
    {
        return $this->hasMany(PollVote::class, 'poll_id');
    }

    public function getTotalVotesAttribute(): int
    {
        return (int) $this->options->sum('votes_count');
    }

    public function getIsExpiredAttribute(): bool
    {
        if ($this->expires_at === null) {
            return false;
        }

        return $this->expires_at->isPast();
    }

    public function hasUserVoted(?int $userId): bool
    {
        if (!$userId) {
            return false;
        }

        return $this->votes()->where('user_id', $userId)->exists();
    }

    public function getUserVotedOptionId(?int $userId): ?int
    {
        if (!$userId) {
            return null;
        }

        return $this->votes()->where('user_id', $userId)->value('poll_option_id');
    }
}

