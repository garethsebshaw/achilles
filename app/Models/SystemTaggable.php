<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemTaggable extends Model
{
    protected $fillable = [
        'tag_id',
        'system_type_id',
        'taggable_type',
        'taggable_id'
    ];

    public function tag()
    {
        return $this->belongsTo(SystemTag::class);
    }

    public function systemType()
    {
        return $this->belongsTo(SystemModule::class);
    }

    public function taggable()
    {
        return $this->morphTo();
    }
}
