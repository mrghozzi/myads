<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    protected $table = 'ads';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'code_ads',
        // Add other columns
    ];
}
