<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemMediaFile extends Model
{
    protected $fillable = [
        'model_type',
        'system_model_id',
        'collection_name',
        'file_name',
        'mime_type',
        'disk',
        'size',
        'metadata'
    ];

    protected $casts = [
        'size' => 'integer',
        'metadata' => 'json'
    ];

    public function model()
    {
        return $this->morphTo();
    }
}
