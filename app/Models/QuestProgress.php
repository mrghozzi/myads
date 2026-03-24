<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestProgress extends Model
{
    use HasFactory;

    protected $table = 'quest_progress';

    protected $fillable = [
        'user_id',
        'quest_id',
        'period_key',
        'progress',
        'completed_at',
        'rewarded_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'rewarded_at' => 'datetime',
    ];

    public function quest()
    {
        return $this->belongsTo(Quest::class, 'quest_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
