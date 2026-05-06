<?php

namespace App\Support\Tenancy;

use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;

class CurrentTenant
{
    protected ?Tenant $tenant = null;

    public function id(): ?int
    {
        return $this->model()?->id;
    }

    public function model(): ?Tenant
    {
        if ($this->tenant instanceof Tenant) {
            return $this->tenant;
        }

        $user = Auth::user();

        if ($user instanceof \App\Models\User && $user->tenant_id) {
            $this->tenant = $user->tenant;

            if ($this->tenant instanceof Tenant) {
                return $this->tenant;
            }
        }

        return $this->tenant = $this->defaultTenant();
    }

    public function set(?Tenant $tenant): void
    {
        $this->tenant = $tenant;
    }

    protected function defaultTenant(): ?Tenant
    {
        $defaultKey = config('rsc_logging.default_tenant_key', 'achilles');

        return Tenant::query()->where('key', $defaultKey)->first()
            ?? Tenant::query()->first();
    }
}
