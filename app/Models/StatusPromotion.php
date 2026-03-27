<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusPromotion extends Model
{
    use HasFactory;

    public const OBJECTIVE_VIEWS = 'views';
    public const OBJECTIVE_COMMENTS = 'comments';
    public const OBJECTIVE_REACTIONS = 'reactions';
    public const OBJECTIVE_DAYS = 'days';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_PAUSED = 'paused';
    public const STATUS_BUDGET_CAPPED = 'budget_capped';

    protected $table = 'status_promotions';

    public $timestamps = false;

    protected $fillable = [
        'status_id',
        'user_id',
        'objective',
        'target_quantity',
        'charged_pts',
        'smart_factor',
        'delivery_cap_impressions',
        'delivered_impressions',
        'baseline_comments',
        'baseline_reactions',
        'status',
        'starts_at',
        'ends_at',
        'completed_at',
        'last_served_at',
        'meta',
    ];

    protected $casts = [
        'charged_pts' => 'integer',
        'smart_factor' => 'decimal:2',
        'delivery_cap_impressions' => 'integer',
        'delivered_impressions' => 'integer',
        'baseline_comments' => 'integer',
        'baseline_reactions' => 'integer',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'completed_at' => 'datetime',
        'last_served_at' => 'datetime',
        'meta' => 'array',
    ];

    public function promotedStatus()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeOngoing($query)
    {
        return $query->whereIn('status', [
            self::STATUS_ACTIVE,
            self::STATUS_PAUSED,
        ]);
    }

    public function isFinal(): bool
    {
        return in_array($this->status, [
            self::STATUS_COMPLETED,
            self::STATUS_EXPIRED,
            self::STATUS_BUDGET_CAPPED,
        ], true);
    }

    public function currentProgressValue(?Status $status = null): int
    {
        $status ??= $this->relationLoaded('promotedStatus') ? $this->getRelation('promotedStatus') : $this->promotedStatus()->first();

        return match ($this->objective) {
            self::OBJECTIVE_VIEWS => (int) $this->delivered_impressions,
            self::OBJECTIVE_COMMENTS => max(0, (int) ($status?->comments_count ?? 0) - (int) $this->baseline_comments),
            self::OBJECTIVE_REACTIONS => max(0, (int) ($status?->reactions_count ?? 0) - (int) $this->baseline_reactions),
            self::OBJECTIVE_DAYS => min(
                (int) $this->target_quantity,
                max(0, (int) optional($this->starts_at)->diffInDays(Carbon::now()))
            ),
            default => 0,
        };
    }

    public function progressPercentage(?Status $status = null): int
    {
        $target = max(1, (int) $this->target_quantity);
        $value = min($target, $this->currentProgressValue($status));

        return (int) min(100, round(($value / $target) * 100));
    }

    public function remainingImpressions(): int
    {
        return max(0, (int) $this->delivery_cap_impressions - (int) $this->delivered_impressions);
    }

    public function expectedImpressionsByNow(?Carbon $now = null): int
    {
        $now ??= Carbon::now();

        if (!$this->starts_at || !$this->ends_at || (int) $this->delivery_cap_impressions <= 0) {
            return max(1, (int) $this->delivered_impressions);
        }

        $totalSeconds = max(1, $this->starts_at->diffInSeconds($this->ends_at, false));
        $elapsedSeconds = min(
            $totalSeconds,
            max(0, $this->starts_at->diffInSeconds($now, false))
        );

        return (int) min(
            (int) $this->delivery_cap_impressions,
            max(1, ceil(((int) $this->delivery_cap_impressions * $elapsedSeconds) / $totalSeconds))
        );
    }

    public function deliveryPacingRatio(?Carbon $now = null): float
    {
        $expected = max(1, $this->expectedImpressionsByNow($now));

        return round(((int) $this->delivered_impressions) / $expected, 4);
    }
}
