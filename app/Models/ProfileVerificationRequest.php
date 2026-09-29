<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileVerificationRequest extends Model
{
    protected $fillable = [
        'user_id', 'reason', 'evidence_links', 'status', 'reviewer_note', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'evidence_links' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
