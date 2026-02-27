<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $table = 'report';
    public $timestamps = false;

    protected $fillable = [
        'uid',
        's_type',
        'tp_id',
        'txt',
        'statu',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'uid');
    }
}
