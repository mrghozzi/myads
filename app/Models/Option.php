<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasPrivacy;

class Option extends Model
{
    use HasFactory, HasPrivacy;

    protected $table = 'options';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'o_valuer',
        'o_type',
        'o_parent',
        'o_order',
        'o_mode',
    ];
}
