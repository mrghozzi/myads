<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModerationLog extends Model
{
    use HasFactory;

    protected $table = 'moderation_logs';
    public $timestamps = false;

    protected $fillable = [
        'moderator_id',
        'target_type',
        'target_id',
        'action',
        'reason',
        'report_id',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'integer',
    ];

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    public function report()
    {
        return $this->belongsTo(Report::class, 'report_id');
    }
}
