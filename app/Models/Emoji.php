<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emoji extends Model
{
    use HasFactory;

    protected $table = 'emojis';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'img',
    ];
}
