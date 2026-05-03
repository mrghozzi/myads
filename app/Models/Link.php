<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    protected $table = 'link';
    public $timestamps = false;

    protected $fillable = [
        'uid',
        'statu',
        'clik',
        'url',
        'name',
        'txt',
        'countries',
        'devices',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'uid');
    }

    public function targetCountries(): array
    {
        return \App\Support\SmartAdTargeting::normalizeCountryCodes($this->countries);
    }

    public function targetDevices(): array
    {
        return \App\Support\SmartAdTargeting::normalizeDeviceTypes($this->devices);
    }
}
