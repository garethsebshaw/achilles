<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SystemCategory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Language extends Model
{
    protected $fillable = [
        'name',
        'iso_code',
        'system_category_id',
        'active',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'json',
        'active' => 'boolean'
    ];

    public function category()
    {
        return $this->belongsTo(SystemCategory::class);
    }

    public function proficiencies()
    {
        return $this->hasMany(LanguageProficiency::class);
    }
}
