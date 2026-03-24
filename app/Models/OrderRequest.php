<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OrderRequest extends Model
{
    use HasFactory;

    protected $table = 'order_requests';

    protected $fillable = [
        'uid',
        'title',
        'description',
        'budget',
        'category',
        'date',
        'statu',
        'best_offer_id',
        'last_activity',
        'avg_rating',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }

    public function bestOffer()
    {
        return $this->belongsTo(Option::class, 'best_offer_id');
    }

    public function offers()
    {
        return $this->hasMany(Option::class, 'o_parent')->where('o_type', 'o_order');
    }

    public function statusRecord()
    {
        return $this->hasOne(Status::class, 'tp_id')->where('s_type', 6);
    }

    public function getDateFormattedAttribute()
    {
        try {
            return Carbon::createFromTimestamp($this->date)->diffForHumans();
        } catch (\Throwable $e) {
            return '';
        }
    }
}
