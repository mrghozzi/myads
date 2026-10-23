<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeveloperWebhookDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'developer_webhook_id',
        'developer_app_id',
        'event',
        'payload',
        'status_code',
        'response_body',
        'duration_ms',
        'attempts',
        'error_message',
        'delivered_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'delivered_at' => 'datetime',
        'status_code' => 'integer',
        'duration_ms' => 'integer',
        'attempts' => 'integer',
    ];

    public function webhook()
    {
        return $this->belongsTo(DeveloperWebhook::class, 'developer_webhook_id');
    }

    public function app()
    {
        return $this->belongsTo(DeveloperApp::class, 'developer_app_id');
    }

    public function isSuccess(): bool
    {
        return $this->status_code >= 200 && $this->status_code < 300;
    }
}
