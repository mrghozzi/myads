<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusRepost extends Model
{
    use HasFactory;

    protected $fillable = [
        'status_id',
        'original_status_id',
        'user_id',
    ];

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function originalStatus()
    {
        return $this->belongsTo(Status::class, 'original_status_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
