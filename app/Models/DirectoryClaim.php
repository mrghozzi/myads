<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectoryClaim extends Model
{
    use HasFactory;

    protected $table = 'directory_claims';
    public $timestamps = false;

    protected $fillable = [
        'directory_id',
        'user_id',
        'verification_method',
        'verification_token',
        'status',
        'notes',
        'created_at',
        'updated_at',
    ];

    public function directory()
    {
        return $this->belongsTo(Directory::class, 'directory_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
