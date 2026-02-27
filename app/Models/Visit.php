<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $table = 'visits';
    public $timestamps = false;

    protected $fillable = [
        'uid',
        'statu',
        'vu',
        'url',
        'name',
        'tims',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }
}
