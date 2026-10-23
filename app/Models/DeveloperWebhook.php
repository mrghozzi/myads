<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeveloperWebhook extends Model
{
    use HasFactory;

    protected $fillable = [
        'developer_app_id',
        'url',
        'secret',
        'events',
        'is_active',
        'failure_count',
    ];

    protected $casts = [
        'events' => 'array',
        'is_active' => 'boolean',
        'failure_count' => 'integer',
    ];

    public function app()
    {
        return $this->belongsTo(DeveloperApp::class, 'developer_app_id');
    }

    public function deliveries()
    {
        return $this->hasMany(DeveloperWebhookDelivery::class, 'developer_webhook_id');
    }

    public function isSubscribedTo(string $event): bool
    {
        $events = (array) ($this->events ?? []);
        return in_array('*', $events, true) || in_array($event, $events, true);
    }
}
