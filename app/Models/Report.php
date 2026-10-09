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
        'category',
        'statu',
        'action_taken',
        'action_notes',
        'moderator_id',
        'resolved_at',
    ];

    public const CATEGORIES = [
        'spam',
        'harassment',
        'inappropriate',
        'copyright',
        'misinformation',
        'scam',
        'other',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'uid');
    }

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    public function logs()
    {
        return $this->hasMany(ModerationLog::class, 'report_id');
    }

    public function isPending(): bool
    {
        return (int) $this->statu === 1;
    }
}

