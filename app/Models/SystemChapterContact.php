<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemChapterContact extends Model
{
    protected $fillable = [
        'system_chapter_id',
        'user_id',
        'name',
        'title',
        'email',
        'phone',
        'is_primary'
    ];

    protected $casts = [
        'is_primary' => 'boolean'
    ];

    public function chapter()
    {
        return $this->belongsTo(SystemChapter::class, 'system_chapter_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
