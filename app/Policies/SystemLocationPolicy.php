<?php

namespace App\Policies;

use App\Models\User;
use App\Models\SystemLocation;
use App\Models\SystemLocationAccess;
use Illuminate\Auth\Access\HandlesAuthorization;

class SystemLocationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true; //return $user->hasPermissionTo('view locations');
    }

    public function view(User $user, SystemLocation $location): bool
    {
        return true; //return $user->hasPermissionTo('view locations');
    }

    public function create(User $user): bool
    {
        return true; //return $user->hasPermissionTo('create locations');
    }

    public function update(User $user, SystemLocation $location): bool
    {
        return true; //return $user->hasPermissionTo('update locations');
    }

    public function delete(User $user, SystemLocation $location): bool
    {
        return true; //return $user->hasPermissionTo('delete locations');
    }

    public function restore(User $user, SystemLocation $location): bool
    {
        return true; //return $user->hasPermissionTo('restore locations');
    }

    public function forceDelete(User $user, SystemLocation $location): bool
    {
        return true; //return $user->hasPermissionTo('force delete locations');
    }

    public function manageAccess(User $user, SystemLocation $location): bool
    {
        return true; //return $user->hasPermissionTo('manage location access');
    }
}
