<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\DeveloperScopeCatalog;

class DeveloperPersonalToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'token',
        'scopes',
        'last_used_at',
        'expires_at',
    ];

    protected $casts = [
        'scopes' => 'array',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        if ($this->expires_at) {
            return $this->expires_at->isPast();
        }
        return false;
    }

    public function hasScope(string $requiredScope): bool
    {
        $scopes = (array) ($this->scopes ?? []);
        $normalizedRequired = DeveloperScopeCatalog::normalizeScopeId($requiredScope);

        if (in_array('*', $scopes, true) || in_array('all', $scopes, true)) {
            return true;
        }

        foreach ($scopes as $scope) {
            if (DeveloperScopeCatalog::normalizeScopeId($scope) === $normalizedRequired) {
                return true;
            }
        }

        return false;
    }
}
