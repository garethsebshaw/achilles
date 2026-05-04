<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certification extends Model
{
    protected $fillable = [
        'certification_type_id',
        'name',
        'description',
        'validity_period',
        'requires_document',
        'system_status_id'
    ];

    protected $casts = [
        'requires_document' => 'boolean',
        'validity_period' => 'integer'
    ];

    public function certificationType()
    {
        return $this->belongsTo(CertificationType::class);
    }

    public function systemStatus()
    {
        return $this->belongsTo(SystemStatus::class)
            ->whereHas('module', function ($query) {
                $query->where('model_type', self::class);
            });
    }

    public function userCertifications()
    {
        return $this->hasMany(UserCertification::class);
    }
}
