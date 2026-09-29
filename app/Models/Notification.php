<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notif'; // Assuming table name is 'notif' based on old code
    public $timestamps = false;

    protected $fillable = [
        'uid',
        'name',
        'nurl',
        'logo',
        'time',
        'state',
    ];

    public function getDisplayNameAttribute(): string
    {
        return match ($this->attributes['name'] ?? '') {
            '@profile_verification_approved' => __('messages.profile_verification_approved'),
            '@profile_verification_rejected' => __('messages.profile_verification_rejected'),
            default => (string) ($this->attributes['name'] ?? ''),
        };
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }
}
