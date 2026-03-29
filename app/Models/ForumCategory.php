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
        'visibility',
    ];

    public function setTxtAttribute($value): void
    {
        // Legacy schemas may keep f_cat.txt as NOT NULL, so normalize empty input.
        $this->attributes['txt'] = $value === null ? '' : (string) $value;
    }

    public function topics()
    {
        return $this->hasMany(ForumTopic::class, 'cat');
    }
}
