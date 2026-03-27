<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityMemberSession extends Model
{
    use HasFactory;

    protected $table = 'security_member_sessions';

    protected $fillable = [
        'session_id',
        'user_id',
        'started_via',
        'ip_address',
        'user_agent',
        'started_at',
        'last_seen_at',
        'ended_at',
        'revoked_at',
        'revoked_by',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'last_seen_at' => 'datetime',
        'ended_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function revokedBy()
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    public function scopeActive($query)
    {
        return $query->whereNull('ended_at')->whereNull('revoked_at');
    }
}
