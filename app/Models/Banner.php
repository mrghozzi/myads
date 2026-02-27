<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $table = 'banner';
    public $timestamps = false;

    protected $fillable = [
        'uid',
        'name',
        'statu',
        'vu',
        'clik',
        'url',
        'img',
        'px',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }
}
