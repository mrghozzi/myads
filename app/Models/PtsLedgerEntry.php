<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PtsLedgerEntry extends Model
{
    use HasFactory;

    protected $table = 'pts_ledger_entries';

    protected $fillable = [
        'transaction_uuid',
        'account_type',
        'account_id',
        'user_id',
        'amount',
        'balance_after',
        'entry_type',
        'reference_type',
        'reference_id',
        'description',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeForUser($query, int|string $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_id', (int) $userId)
              ->orWhere('account_id', 'user:' . (int) $userId);
        });
    }

    public function scopeTransaction($query, string $uuid)
    {
        return $query->where('transaction_uuid', $uuid);
    }

    public function scopeSystemAccounts($query)
    {
        return $query->where('account_type', '!=', 'user');
    }

    public function isCredit(): bool
    {
        return (float) $this->amount > 0;
    }

    public function isDebit(): bool
    {
        return (float) $this->amount < 0;
    }
}

