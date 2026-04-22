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
}
