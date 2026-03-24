<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteAdmin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'is_super',
        'has_full_access',
        'permissions',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_super' => 'boolean',
        'has_full_access' => 'boolean',
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
