<?php

namespace App\Policies\Concerns;

use App\Models\User;

trait AuthorizesTenantScopedLogs
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSysAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasPrivilegedRole();
    }

    public function view(User $user, object $model): bool
    {
        if (! $user->hasPrivilegedRole()) {
            return false;
        }

        if ($model->tenant_id === null) {
            return false;
        }

        return (int) $user->tenant_id === (int) $model->tenant_id;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, object $model): bool
    {
        return false;
    }

    public function delete(User $user, object $model): bool
    {
        return false;
    }

    public function restore(User $user, object $model): bool
    {
        return false;
    }

    public function forceDelete(User $user, object $model): bool
    {
        return false;
    }
}
