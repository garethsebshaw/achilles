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
    use HasFactory, Notifiable, SoftDeletes;

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
            'is_subscribed' => 'boolean',
            'is_sys_admin' => 'boolean',
            'is_admin' => 'boolean',
            'is_team_leader' => 'boolean',
            'is_athlete' => 'boolean',
            'is_guide' => 'boolean',
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
        return (bool) $this->is_sys_admin;
    }

    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    public function isTeamLeader(): bool
    {
        return (bool) $this->is_team_leader;
    }

    public function hasPrivilegedRole(): bool
    {
        return $this->isSysAdmin() || $this->isAdmin() || $this->isTeamLeader();
    }

    public function canAccessNova(): bool
    {
        return $this->hasPrivilegedRole();
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
