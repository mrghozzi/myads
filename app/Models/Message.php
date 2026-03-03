<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = 'messages';
    protected $primaryKey = 'id_msg';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'us_env', // Sender ID
        'us_rec', // Receiver ID
        'msg',
        'attachment_path',
        'attachment_name',
        'attachment_size',
        'time',
        'state',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'us_env');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'us_rec');
    }

    public function getTextAttribute()
    {
        return $this->msg;
    }

    public function setTextAttribute($value)
    {
        $this->attributes['msg'] = $value;
    }

    public function getIdAttribute()
    {
        return $this->attributes['id_msg'] ?? null;
    }
}
