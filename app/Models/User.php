<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserCertification;
use App\Models\LanguageProficiency;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, TwoFactorAuthenticatable;

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
        'two_factor_secret',
        'two_factor_recovery_codes',
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
            'two_factor_confirmed_at' => 'datetime',
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

    public function workoutSignups(): HasMany
    {
        return $this->hasMany(WorkoutSignup::class, 'user_id');
    }

    public function locationAccessRecords(): HasMany
    {
        return $this->hasMany(SystemLocationAccess::class, 'user_id');
    }

    public function accessibleLocations(): BelongsToMany
    {
        return $this->belongsToMany(SystemLocation::class, 'system_location_access', 'user_id', 'location_id')
            ->withPivot([
                'access_type',
                'access_identifier',
                'access_granted_date',
                'access_expiry_date',
                'granted_by_id',
                'is_active',
                'notes',
            ]);
    }

    public function activeLocationAccessRecords(): HasMany
    {
        return $this->locationAccessRecords()->valid();
    }

    public function notifications(): MorphMany
    {
        return $this->morphMany(SystemNotification::class, 'notifiable')->latest();
    }

    public function readNotifications(): MorphMany
    {
        return $this->notifications()->read();
    }

    public function unreadNotifications(): MorphMany
    {
        return $this->notifications()->unread();
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

    public function accessibleLocationIds(): array
    {
        return $this->activeLocationAccessRecords()
            ->pluck('location_id')
            ->unique()
            ->values()
            ->all();
    }

    public function accessibleChapterIds(): array
    {
        $locationIds = $this->accessibleLocationIds();

        if ($locationIds === []) {
            return [];
        }

        return SystemLocation::query()
            ->whereIn('id', $locationIds)
            ->whereNotNull('chapter_id')
            ->pluck('chapter_id')
            ->unique()
            ->values()
            ->all();
    }

    public function hasActiveChapterAccess(int $chapterId): bool
    {
        return in_array($chapterId, $this->accessibleChapterIds(), true);
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

        return __('Users');
    }
    public static function authorizedToViewAny()
    {
        return true;
    }
}
