<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpRating extends Model
{
    use HasFactory;

    protected $table = 'help_ratings';

    protected $fillable = [
        'article_slug',
        'is_helpful',
        'ip_address',
        'user_id',
    ];

    protected $casts = [
        'is_helpful' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Retrieve statistics for an article and check if the current visitor already voted.
     */
    public static function getStats(string $articleSlug, ?int $userId = null, ?string $ipAddress = null): array
    {
        $query = static::where('article_slug', $articleSlug);

        $helpfulCount = (clone $query)->where('is_helpful', true)->count();
        $unhelpfulCount = (clone $query)->where('is_helpful', false)->count();
        $total = $helpfulCount + $unhelpfulCount;

        $userVoted = null;
        if ($userId) {
            $existing = static::where('article_slug', $articleSlug)->where('user_id', $userId)->first();
            if ($existing) {
                $userVoted = $existing->is_helpful ? 'yes' : 'no';
            }
        } elseif ($ipAddress) {
            $existing = static::where('article_slug', $articleSlug)->where('ip_address', $ipAddress)->first();
            if ($existing) {
                $userVoted = $existing->is_helpful ? 'yes' : 'no';
            }
        }

        $percentage = $total > 0 ? (int) round(($helpfulCount / $total) * 100) : 100;

        return [
            'helpful_count' => $helpfulCount,
            'unhelpful_count' => $unhelpfulCount,
            'total' => $total,
            'percentage' => $percentage,
            'user_voted' => $userVoted,
        ];
    }

    /**
     * Record or update a visitor's vote.
     */
    public static function recordVote(string $articleSlug, bool $isHelpful, ?int $userId = null, ?string $ipAddress = null): array
    {
        $record = null;

        if ($userId) {
            $record = static::where('article_slug', $articleSlug)->where('user_id', $userId)->first();
        } elseif ($ipAddress) {
            $record = static::where('article_slug', $articleSlug)->where('ip_address', $ipAddress)->first();
        }

        if ($record) {
            $record->is_helpful = $isHelpful;
            if ($userId && !$record->user_id) {
                $record->user_id = $userId;
            }
            $record->save();
        } else {
            static::create([
                'article_slug' => $articleSlug,
                'is_helpful' => $isHelpful,
                'user_id' => $userId,
                'ip_address' => $ipAddress,
            ]);
        }

        return static::getStats($articleSlug, $userId, $ipAddress);
    }
}
