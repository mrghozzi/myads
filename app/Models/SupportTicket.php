<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    use HasFactory;

    protected $table = 'support_tickets';

    protected $fillable = [
        'ticket_number',
        'user_id',
        'department',
        'priority',
        'status',
        'subject',
        'last_reply_at',
        'last_reply_by',
        'closed_at',
        'closed_by',
    ];

    protected $casts = [
        'last_reply_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function messages()
    {
        return $this->hasMany(SupportTicketMessage::class, 'ticket_id')->orderBy('created_at', 'asc');
    }

    public function lastReplier()
    {
        return $this->belongsTo(User::class, 'last_reply_by');
    }

    public function closer()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function isClosed(): bool
    {
        return in_array($this->status, ['closed', 'resolved']);
    }

    public static function generateTicketNumber(): string
    {
        $year = date('Y');
        $random = strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
        $count = self::whereYear('created_at', $year)->count() + 1;
        return sprintf('TKT-%s-%04d-%s', $year, $count, $random);
    }
}
