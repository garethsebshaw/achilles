<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserCertification;
use App\Models\LanguageProficiency;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'first_name',
        'middle_name',
        'last_name',
        'preferred_name',
        'phone',
        'picture',
        'is_subscribed',
        'is_team_leader',
        'is_athlete',
        'is_guide',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function userCertification()
    {
        return $this->hasMany(UserCertification::class);
    }

    public function languageProficiency()
    {
        return $this->hasMany(LanguageProficiency::class);
    }

    public function isSysAdmin(): bool
    {
        // Implement your admin check logic
        // This could be based on a role, a specific column, or any other condition
        return $this->is_sys_admin === 1; // Example

        // Or if you have an is_admin column
        // return (bool) $this->is_admin;

        // Or check against a list of admin emails
        // return in_array($this->email, config('admin.emails', []));
    }

    public function isAdmin(): bool
    {
        // Implement your admin check logic
        // This could be based on a role, a specific column, or any other condition
        return $this->is_admin === 1; // Example

        // Or if you have an is_admin column
        // return (bool) $this->is_admin;

        // Or check against a list of admin emails
        // return in_array($this->email, config('admin.emails', []));
    }

    public function isTeamLeader(): bool
    {
        // Implement your admin check logic
        // This could be based on a role, a specific column, or any other condition
        return $this->is_team_leader === 1; // Example

        // Or if you have an is_admin column
        // return (bool) $this->is_admin;

        // Or check against a list of admin emails
        // return in_array($this->email, config('admin.emails', []));
    }

    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'users';
    }

    public static function label() {

        return 'Users';
    }
    public static function authorizedToViewAny()
    {
        return true;
    }
}
