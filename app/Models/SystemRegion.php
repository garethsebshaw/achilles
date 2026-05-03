<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SystemChapter;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemRegion extends Model
{
    protected $fillable = ['name', 'code', 'description', 'active'];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function chapters()
    {
        return $this->hasMany(SystemChapter::class, 'system_region_id');
    }
}
