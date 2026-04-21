<?php

namespace App\Models;

use App\Support\OrderCategoryOptions;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderRequest extends Model
{
    use HasFactory;

    public const WORKFLOW_OPEN = 'open';
    public const WORKFLOW_AWARDED = 'awarded';
    public const WORKFLOW_IN_PROGRESS = 'in_progress';
    public const WORKFLOW_DELIVERED = 'delivered';
    public const WORKFLOW_COMPLETED = 'completed';
    public const WORKFLOW_CANCELLED = 'cancelled';
    public const WORKFLOW_CLOSED = 'closed';

    public const PRICING_FIXED = 'fixed';
    public const PRICING_RANGE = 'range';
    public const PRICING_NEGOTIABLE = 'negotiable';

    protected $table = 'order_requests';

    protected $fillable = [
        'uid',
        'title',
        'description',
        'budget',
        'category',
        'pricing_model',
        'budget_min',
        'budget_max',
        'budget_currency',
        'delivery_window_days',
        'date',
        'statu',
        'best_offer_id',
        'last_activity',
        'avg_rating',
        'workflow_status',
    ];

    protected $casts = [
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'avg_rating' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }

    public function bestOffer()
    {
        return $this->belongsTo(OrderOffer::class, 'best_offer_id');
    }

    public function awardedOffer()
    {
        return $this->belongsTo(OrderOffer::class, 'best_offer_id');
    }

    public function offers()
    {
        return $this->hasMany(OrderOffer::class, 'order_request_id')->latest('created_at');
    }

    public function activeOffers()
    {
        return $this->offers()->whereIn('status', [
            OrderOffer::STATUS_ACTIVE,
            OrderOffer::STATUS_AWARDED,
            OrderOffer::STATUS_ARCHIVED,
        ]);
    }

    public function contract()
    {
        return $this->hasOne(OrderContract::class, 'order_request_id');
    }

    public function statusRecord()
    {
        return $this->hasOne(Status::class, 'tp_id')->where('s_type', 6);
    }

    public function getDateFormattedAttribute()
    {
        try {
            return Carbon::createFromTimestamp($this->date)->diffForHumans();
        } catch (\Throwable $e) {
            return '';
        }
    }

    public function getDerivedWorkflowStatusAttribute(): string
    {
        $status = (string) ($this->workflow_status ?: self::WORKFLOW_OPEN);
        $offersCount = $this->offers_count ?? null;

        if (
            $status === self::WORKFLOW_OPEN
            && (($offersCount !== null && (int) $offersCount > 0) || ($offersCount === null && $this->offers()->exists()))
        ) {
            return 'under_review';
        }

        return $status;
    }

    public function getLegacyBudgetTextAttribute(): string
    {
        if (trim((string) $this->budget) !== '') {
            return (string) $this->budget;
        }

        return $this->displayBudget();
    }

    public function displayBudget(): string
    {
        $currency = trim((string) ($this->budget_currency ?: 'USD'));

        if ($this->pricing_model === self::PRICING_NEGOTIABLE || ($this->budget_min === null && $this->budget_max === null)) {
            return __('messages.order_budget_negotiable');
        }

        $min = $this->budget_min !== null ? (float) $this->budget_min : null;
        $max = $this->budget_max !== null ? (float) $this->budget_max : null;

        if ($min !== null && $max !== null && abs($min - $max) < 0.01) {
            return $currency . ' ' . number_format($min, 2);
        }

        if ($min !== null && $max !== null) {
            return __('messages.order_budget_range_value', [
                'currency' => $currency,
                'min' => number_format($min, 2),
                'max' => number_format($max, 2),
            ]);
        }

        $amount = $min ?? $max;
        if ($amount === null) {
            return __('messages.order_budget_negotiable');
        }

        return $currency . ' ' . number_format((float) $amount, 2);
    }

    public function displayCategory(): string
    {
        return OrderCategoryOptions::label($this->category);
    }

    public function displayDeliveryWindow(): string
    {
        if (!$this->delivery_window_days) {
            return __('messages.order_delivery_flexible');
        }

        return __('messages.order_delivery_days_value', ['days' => $this->delivery_window_days]);
    }

    public function displayWorkflowStatus(): string
    {
        return __('messages.order_status_' . $this->derived_workflow_status);
    }

    public function isOpenForOffers(): bool
    {
        return (string) $this->workflow_status === self::WORKFLOW_OPEN && (int) $this->statu === 1;
    }

    public function isManagedWorkflow(): bool
    {
        return in_array((string) $this->workflow_status, [
            self::WORKFLOW_AWARDED,
            self::WORKFLOW_IN_PROGRESS,
            self::WORKFLOW_DELIVERED,
        ], true);
    }

    public function isTerminal(): bool
    {
        return in_array((string) $this->workflow_status, [
            self::WORKFLOW_COMPLETED,
            self::WORKFLOW_CANCELLED,
            self::WORKFLOW_CLOSED,
        ], true);
    }

    public function syncLifecycleState(string $workflowStatus): void
    {
        $this->workflow_status = $workflowStatus;
        $this->statu = in_array($workflowStatus, [
            self::WORKFLOW_COMPLETED,
            self::WORKFLOW_CANCELLED,
            self::WORKFLOW_CLOSED,
        ], true) ? 0 : 1;
        $this->last_activity = time();
    }
}
