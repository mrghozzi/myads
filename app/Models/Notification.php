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

    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }
}
