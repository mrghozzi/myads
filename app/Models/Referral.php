<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;

    protected $table = 'referral';
    public $timestamps = false;

    protected $fillable = [
        'uid',
        'ruid',
        'date',
    ];

    public function referrer()
    {
        return $this->belongsTo(User::class, 'uid');
    }

    public function referredUser()
    {
        return $this->belongsTo(User::class, 'ruid');
    }
}
