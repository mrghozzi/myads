<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeoRule extends Model
{
    use HasFactory;

    protected $table = 'seo_rules';

    protected $fillable = [
        'scope_key',
        'content_type',
        'content_id',
        'title',
        'description',
        'keywords',
        'robots',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image_url',
        'twitter_card',
        'schema_type',
        'indexable',
        'is_active',
    ];

    protected $casts = [
        'indexable' => 'boolean',
        'is_active' => 'boolean',
    ];
}
