<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCertification extends Model
{
    protected $fillable = [
        'user_id',
        'certification_id',
        'certified_at',
        'expires_at',
        'name',
        'description',
        'validity_period',
        'file_path',
        'file_type',
        'uploaded_at',
        'system_status_id',
        'notes'
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'certified_at' => 'date',
        'expires_at' => 'date',
        'validity_period' => 'integer'
    ];

    protected $table = 'user_certifications';

    public function setDocumentAttribute($value)
    {
        // If a file is being uploaded
        if ($value && is_file($value)) {
            // Store the file in the public disk under 'certification-documents'
            $path = $value->store('certification-documents', 'public');

            // Set the document path
            $this->attributes['document_path'] = $path;

            // Set the file type
            $this->attributes['file_type'] = $value->getClientOriginalExtension();
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function certification()
    {
        return $this->belongsTo(Certification::class, 'certification_id');
    }

    public function systemStatus()
    {
        return $this->belongsTo(SystemStatus::class, 'system_status_id');
    }

    public function documents()
    {
        return $this->hasMany(CertificationDocument::class, 'certification_id');
    }
}
