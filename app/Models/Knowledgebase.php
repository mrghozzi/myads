<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Knowledgebase extends Model
{
    use HasFactory;

    protected $table = 'options';
    public $timestamps = false;

    protected $fillable = [
        'name',      // Title
        'o_valuer',  // Content
        'o_type',    // 'knowledgebase'
        'o_mode',    // Category
        'o_order',   // Order
    ];

    protected static function booted()
    {
        static::addGlobalScope('knowledgebase', function (Builder $builder) {
            $builder->where('o_type', 'knowledgebase')->where('o_order', 0);
        });
        
        static::creating(function ($model) {
            $model->o_type = 'knowledgebase';
            if (!isset($model->o_order)) {
                $model->o_order = 0;
            }
        });
    }
}
