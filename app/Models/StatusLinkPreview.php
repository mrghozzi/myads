<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusLinkPreview extends Model
{
    use HasFactory;

    protected $fillable = [
        'status_id',
        'url',
        'normalized_url',
        'title',
        'description',
        'image_url',
        'site_name',
        'domain',
        'directory_id',
        'directory_status_id',
    ];

    public function status()
    {
        return $this->belongsTo(Status::class, 'status_id');
    }

    public function directory()
    {
        return $this->belongsTo(Directory::class, 'directory_id');
    }

    public function directoryStatus()
    {
        return $this->belongsTo(Status::class, 'directory_status_id');
    }
}
