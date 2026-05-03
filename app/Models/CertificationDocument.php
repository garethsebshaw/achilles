<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificationDocument extends Model
{
    protected $fillable = [
        'certification_type_id',
        'name',
        'description',
        'validity_period',
        'requires_document',
        'user_certification_id',
        'file_path',
        'file_type',
        'uploaded_at'
    ];

    protected $casts = [
        'requires_document' => 'boolean',
        'validity_period' => 'integer',
        'uploaded_at' => 'datetime'
    ];

    public function userCertification()
    {
        return $this->belongsTo(UserCertification::class);
    }

    public function certificationType()
    {
        return $this->belongsTo(CertificationType::class);
    }
}
