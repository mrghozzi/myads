<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'email',
        'pass',
        'img',
        'ucheck',
        'online',
        // 'active', // Column 'active' not found in DB
        'pts',
        'vu',
        'nvu',
        'nlink',
        'sig',
        'email_verified_at',
    ];

    protected $hidden = [
        'pass',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->pass;
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }

    // Accessors for compatibility
    public function getNameAttribute()
    {
        return $this->username;
    }

    public function getAvatarAttribute()
    {
        return $this->img;
    }
    
    public function isAdmin()
    {
        return $this->id == 1;
    }

    public function isOnline()
    {
        return $this->online > (time() - 240);
    }

    // Relationships
    public function topics()
    {
        return $this->hasMany(ForumTopic::class, 'uid');
    }

    public function comments()
    {
        return $this->hasMany(ForumComment::class, 'uid');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'o_parent');
    }

    public function listings()
    {
        return $this->hasMany(Directory::class, 'uid');
    }

    public function visits()
    {
        return $this->hasMany(Visit::class, 'uid');
    }
}
