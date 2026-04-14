<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BillingTransaction extends Model
{
    use HasFactory;

    protected $table = 'billing_transactions';

    protected $fillable = [
        'billing_order_id',
        'user_id',
        'gateway',
        'transaction_type',
        'status',
        'external_transaction_id',
        'amount',
        'currency_code',
        'exchange_rate_snapshot',
        'processed_at',
        'meta',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate_snapshot' => 'decimal:6',
        'processed_at' => 'datetime',
        'meta' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(BillingOrder::class, 'billing_order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function gatewayLabel(): string
    {
        $translationKey = match ((string) $this->gateway) {
            'stripe' => 'messages.billing_gateway_stripe',
            'paypal' => 'messages.billing_gateway_paypal',
            'bank_transfer' => 'messages.billing_gateway_bank_transfer',
            default => null,
        };

        if ($translationKey !== null) {
            $translation = __($translationKey);
            if ($translation !== $translationKey) {
                return $translation;
            }
        }

        return Str::headline(str_replace('_', ' ', (string) $this->gateway));
    }

    public function transactionTypeLabel(): string
    {
        $translationKey = 'messages.billing_transaction_type_' . (string) $this->transaction_type;
        $translation = __($translationKey);

        if ($translation !== $translationKey) {
            return $translation;
        }

        return Str::headline(str_replace('_', ' ', (string) $this->transaction_type));
    }
}
