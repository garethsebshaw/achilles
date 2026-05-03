<?php
namespace App\Traits;

use Illuminate\Support\Facades\Gate;

trait HasEquipmentAuthorization
{
    public function canManageEquipment(): bool
    {
        return Gate::allows('manage-equipment');
    }

    public function canViewEquipment(): bool
    {
        return Gate::allows('view-equipment');
    }

    public function canCreateEquipment(): bool
    {
        return Gate::allows('create-equipment');
    }

    public function canUpdateEquipment(): bool
    {
        return Gate::allows('update-equipment');
    }

    public function canDeleteEquipment(): bool
    {
        return Gate::allows('delete-equipment');
    }

    public function canManageMaintenance(): bool
    {
        return Gate::allows('manage-maintenance');
    }

    public function canAssignMaintenance(): bool
    {
        return Gate::allows('assign-maintenance');
    }
}
