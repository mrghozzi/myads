<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityIpBan extends Model
{
    use HasFactory;

    protected $table = 'security_ip_bans';

    protected $fillable = [
        'ip_address',
        'reason',
        'is_active',
        'expires_at',
        'banned_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function bannedBy()
    {
        return $this->belongsTo(User::class, 'banned_by');
    }
}
