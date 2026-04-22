<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeveloperAuthorization extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'developer_app_id',
        'scopes',
    ];

    protected $casts = [
        'scopes' => 'array',
    ];

    /**
     * The user who granted the authorization.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The app that was granted authorization.
     */
    public function app()
    {
        return $this->belongsTo(DeveloperApp::class, 'developer_app_id');
    }
}
