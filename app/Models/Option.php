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
        'updated_at',
        'kb_category_id',
    ];

    /**
     * Get the KB category this article belongs to.
     */
    public function kbCategory()
    {
        return $this->belongsTo(KbCategory::class, 'kb_category_id');
    }

    /**
     * Get the user who authored this option (used when option acts as a comment).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'o_order');
    }
}
