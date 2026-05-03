<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemChapter extends Model
{
    protected $fillable = [
        'system_country_id',
        'system_region_id',
        'name',
        'city',
        'state',
        'postal_code',
        'email',
        'phone',
        'website',
        'social_media',
        'is_headquarters',
        'active',
        'metadata'
    ];

    protected $casts = [
        'social_media' => 'json',
        'metadata' => 'json',
        'is_headquarters' => 'boolean',
        'active' => 'boolean'
    ];

    public function country()
    {
        return $this->belongsTo(SystemCountry::class, 'system_country_id');
    }

    public function region()
    {
        return $this->belongsTo(SystemRegion::class, 'system_region_id');
    }

    public function contacts()
    {
        return $this->hasMany(SystemChapterContact::class);
    }
}
