<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PtsVoucher extends Model
{
    use HasFactory;

    protected $table = 'pts_vouchers';

    protected $fillable = [
        'user_id',
        'code',
        'amount',
        'is_used',
        'used_by',
        'used_at',
    ];

    protected $casts = [
        'is_used' => 'boolean',
        'amount' => 'double',
        'used_at' => 'datetime',
    ];

    /**
     * Get the user who generated the voucher.
     */
    public function generator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who claimed the voucher.
     */
    public function claimer()
    {
        return $this->belongsTo(User::class, 'used_by');
    }
}
