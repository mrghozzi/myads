<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Short extends Model
{
    use HasFactory;

    protected $table = 'short';
    public $timestamps = false;

    protected $fillable = [
        'uid',
        'url',
        'sho',
        'clik',
        'sh_type',
        'tp_id',
    ];
}
