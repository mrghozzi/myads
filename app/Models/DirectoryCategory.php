<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectoryCategory extends Model
{
    use HasFactory;

    protected $table = 'cat_dir';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'sub',
        'ordercat',
        'statu',
        'txt',
        'metakeywords',
    ];

    public function parent()
    {
        return $this->belongsTo(DirectoryCategory::class, 'sub', 'id');
    }

    public function children()
    {
        return $this->hasMany(DirectoryCategory::class, 'sub', 'id');
    }
}
