<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Certification;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemCountry extends Model
{
    protected $fillable = [
        'name',
        'short_name',
        'iso2',
        'iso3',
        'numeric_code',
        'calling_code',
        'capital',
        'currency',
        'currency_symbol',
        'active',
        'metadata'
    ];

    protected $casts = [
        'active' => 'boolean',
        'metadata' => 'json'
    ];

    public function certifications()
    {
        return $this->belongsToMany(Certification::class, 'certification_countries');
    }
}
