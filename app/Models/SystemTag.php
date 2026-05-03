<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemTag extends Model
{
    protected $fillable = ['parent_id', 'name', 'type', 'color'];

    public function parent()
    {
        return $this->belongsTo(SystemTag::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(SystemTag::class, 'parent_id');
    }

    public function taggables()
    {
        return $this->hasMany(SystemTaggable::class, 'tag_id');
    }
}
