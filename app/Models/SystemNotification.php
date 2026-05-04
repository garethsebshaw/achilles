<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\DatabaseNotification;

class SystemNotification extends DatabaseNotification
{
    use SoftDeletes;

    protected $table = 'system_notifications';

    public function getTitleAttribute(): string
    {
        return (string) data_get($this->data, 'title', class_basename((string) $this->type));
    }

    public function getMessageAttribute(): ?string
    {
        $message = data_get($this->data, 'message');

        return is_string($message) && $message !== '' ? $message : null;
    }
}
