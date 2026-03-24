<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HasPrivacy;

class Directory extends Model
{
    use HasFactory, HasPrivacy;

    protected $table = 'directory';
    public $timestamps = false;

    protected $fillable = [
        'uid',
        'name',
        'url',
        'txt',
        'metakeywords',
        'cat',
        'vu',
        'statu',
        'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }

    public function category()
    {
        return $this->belongsTo(DirectoryCategory::class, 'cat');
    }
}
