<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeveloperAccessToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'developer_app_id',
        'user_id',
        'access_token',
        'scopes',
        'expires_at',
        'revoked',
    ];

    protected $casts = [
        'scopes' => 'array',
        'expires_at' => 'datetime',
        'revoked' => 'boolean',
    ];

    public function app()
    {
        return $this->belongsTo(DeveloperApp::class, 'developer_app_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function refreshToken()
    {
        return $this->hasOne(DeveloperRefreshToken::class);
    }

    public function isExpired()
    {
        if ($this->expires_at) {
            return $this->expires_at->isPast();
        }

        if ($this->created_at) {
            return $this->created_at->addDays(30)->isPast();
        }

        return false;
    }

    public function isValid()
    {
        return !$this->revoked && !$this->isExpired();
    }
}
