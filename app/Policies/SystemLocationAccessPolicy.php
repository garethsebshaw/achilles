<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SystemLocation;
use App\Models\SystemLocationAccess;
use Illuminate\Auth\Access\HandlesAuthorization;

class SystemLocationAccessPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true; //return $user->hasPermissionTo('view location access');
    }

    public function view(User $user, SystemLocationAccess $access): bool
    {
        return true; //return $user->hasPermissionTo('view location access');
    }

    public function create(User $user): bool
    {
        return true; //return $user->hasPermissionTo('manage location access');
    }

    public function update(User $user, SystemLocationAccess $access): bool
    {
        return true; //return $user->hasPermissionTo('manage location access');
    }

    public function delete(User $user, SystemLocationAccess $access): bool
    {
        return true; //return $user->hasPermissionTo('manage location access');
    }

    public function restore(User $user, SystemLocationAccess $access): bool
    {
        return true; //return $user->hasPermissionTo('manage location access');
    }

    public function forceDelete(User $user, SystemLocationAccess $access): bool
    {
        return true; //return $user->hasPermissionTo('manage location access');
    }
}
