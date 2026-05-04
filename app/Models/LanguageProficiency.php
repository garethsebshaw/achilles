<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LanguageProficiency extends Model
{
    protected $fillable = [
        'user_id',
        'language_id',
        'proficiency_status_id',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'json'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function communicationMode()
    {
        return $this->belongsTo(SystemCategory::class, 'communication_mode_id');
    }

    public function proficiencyStatus()
    {
        return $this->belongsTo(SystemStatus::class, 'proficiency_status_id')
            ->whereHas('module', function ($query) {
                $query->where('model_type', self::class);
            });
    }
}
