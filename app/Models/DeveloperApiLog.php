<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeveloperApiLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'developer_app_id',
        'user_id',
        'token_type',
        'endpoint',
        'method',
        'status_code',
        'duration_ms',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'status_code' => 'integer',
        'duration_ms' => 'integer',
        'created_at' => 'datetime',
    ];

    public function app()
    {
        return $this->belongsTo(DeveloperApp::class, 'developer_app_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
