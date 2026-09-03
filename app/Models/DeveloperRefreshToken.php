<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeveloperRefreshToken extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'developer_access_token_id',
        'refresh_token',
        'expires_at',
        'revoked',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'revoked' => 'boolean',
    ];

    public function accessToken()
    {
        return $this->belongsTo(DeveloperAccessToken::class, 'developer_access_token_id');
    }

    public function isExpired()
    {
        if ($this->expires_at) {
            return $this->expires_at->isPast();
        }

        if ($this->created_at) {
            return $this->created_at->addDays(90)->isPast();
        }

        return false;
    }

    public function isValid()
    {
        return !$this->revoked && !$this->isExpired();
    }
}
