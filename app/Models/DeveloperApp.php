<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeveloperApp extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'domain',
        'description',
        'logo',
        'status',
        'client_id',
        'client_secret',
        'redirect_uris',
        'requested_scopes',
        'widget_capabilities',
    ];

    protected $casts = [
        'redirect_uris' => 'array',
        'requested_scopes' => 'array',
        'widget_capabilities' => 'array',
    ];

    /**
     * The owner of the app.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The authorizations granted to this app.
     */
    public function authorizations()
    {
        return $this->hasMany(DeveloperAuthorization::class);
    }

    /**
     * Check if the app is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if the app is usable by a specific user (active or owned by the user in development mode).
     */
    public function isUsableBy(?User $user = null): bool
    {
        if ($this->isActive()) {
            return true;
        }

        if ($user && (int) $this->user_id === (int) $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Ensure requested_scopes attribute always returns a clean array of strings.
     */
    public function getRequestedScopesAttribute($value): array
    {
        if (is_array($value)) {
            return array_values(array_filter($value));
        }

        if (is_string($value) && trim($value) !== '') {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return array_values(array_filter($decoded));
            }
            return array_values(array_filter(array_map('trim', preg_split('/[\s,]+/', $value))));
        }

        return [];
    }

    /**
     * Ensure redirect_uris attribute always returns a clean array of URI strings.
     */
    public function getRedirectUrisAttribute($value): array
    {
        if (is_array($value)) {
            return array_values(array_filter($value));
        }

        if (is_string($value) && trim($value) !== '') {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                return array_values(array_filter($decoded));
            }
            return array_values(array_filter(array_map('trim', preg_split('/[\r\n,]+/', $value))));
        }

        return [];
    }
}
