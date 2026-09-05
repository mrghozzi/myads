<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeveloperAccessToken extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'developer_app_id',
        'user_id',
        'access_token',
        'scopes',
        'expires_at',
        'revoked',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'revoked' => 'boolean',
    ];

    public function getScopesAttribute($value): array
    {
        if (is_array($value)) {
            $raw = $value;
        } elseif (is_string($value)) {
            $decoded = json_decode($value, true);
            $raw = is_array($decoded) ? $decoded : preg_split('/[\s,]+/', trim($value));
        } else {
            $raw = [];
        }

        $result = [];
        foreach ($raw as $item) {
            if (is_string($item)) {
                foreach (preg_split('/[\s,]+/', trim($item)) as $sub) {
                    if ($sub !== '') {
                        $result[] = $sub;
                    }
                }
            }
        }

        return array_values(array_unique($result));
    }

    public function setScopesAttribute($value): void
    {
        if (is_array($value)) {
            $this->attributes['scopes'] = json_encode(array_values($value));
        } elseif (is_string($value)) {
            $trimmed = trim($value);
            if (str_starts_with($trimmed, '[') || str_starts_with($trimmed, '{')) {
                $this->attributes['scopes'] = $trimmed;
            } else {
                $parts = array_values(array_filter(preg_split('/[\s,]+/', $trimmed)));
                $this->attributes['scopes'] = json_encode($parts);
            }
        } else {
            $this->attributes['scopes'] = json_encode([]);
        }
    }

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
