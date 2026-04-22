<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeveloperAuthorizationCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'developer_app_id',
        'user_id',
        'code',
        'redirect_uri',
        'scopes',
        'expires_at',
        'used',
    ];

    protected $casts = [
        'scopes' => 'array',
        'expires_at' => 'datetime',
        'used' => 'boolean',
    ];

    public function app()
    {
        return $this->belongsTo(DeveloperApp::class, 'developer_app_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }
}
