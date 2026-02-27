<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'setting';
    public $timestamps = false;

    protected $fillable = [
        'titer',
        'description',
        'url',
        'styles',
        'lang',
        'timezone',
        'close',
        'close_text',
        'a_mail',
        'a_not',
        'e_links',
        'facebook',
        'twitter',
        'linkedin',
    ];
}
