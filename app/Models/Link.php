<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    protected $table = 'link';
    public $timestamps = false;

    protected $fillable = [
        'uid',
        'statu',
        'clik',
        'url',
        'name',
        'txt',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }
}
