<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'is_encrypted', 'description'];

    protected $casts = [
        'is_encrypted' => 'boolean',
        'value' => 'encrypted'
    ];

    protected function getValueAttribute($value)
    {
        if ($this->is_encrypted) {
            return decrypt($value);
        }
        return $value;
    }

    protected function setValueAttribute($value)
    {
        if ($this->is_encrypted) {
            $this->attributes['value'] = encrypt($value);
        } else {
            $this->attributes['value'] = $value;
        }
    }
}
