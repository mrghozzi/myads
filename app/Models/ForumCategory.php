<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForumCategory extends Model
{
    use HasFactory;

    protected $table = 'f_cat';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'icons',
        'txt',
        'ordercat',
    ];
}
