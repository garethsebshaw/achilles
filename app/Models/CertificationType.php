<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Certification;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificationType extends Model
{
    protected $fillable = [
        'name',
        'description',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean'
    ];

    public function certifications()
    {
        return $this->hasMany(Certification::class);
    }
}
