<?php
namespace App\Traits;

use Carbon\Carbon;

trait HasCheckouts
{
    public function isCheckedOut(): bool
    {
        return $this->checkouts()
            ->whereNull('returned_at')
            ->exists();
    }

    public function getCurrentCheckout()
    {
        return $this->checkouts()
            ->whereNull('returned_at')
            ->latest('checked_out_at')
            ->first();
    }

    public function getLastCheckout()
    {
        return $this->checkouts()
            ->whereNotNull('returned_at')
            ->latest('returned_at')
            ->first();
    }

    public function isOverdue(): bool
    {
        $currentCheckout = $this->getCurrentCheckout();
        if (!$currentCheckout || !$currentCheckout->expected_return_at) {
            return false;
        }
        return Carbon::now()->greaterThan($currentCheckout->expected_return_at);
    }

    public function getDaysCheckedOut(): ?int
    {
        $currentCheckout = $this->getCurrentCheckout();
        if (!$currentCheckout) {
            return null;
        }
        return Carbon::parse($currentCheckout->checked_out_at)->diffInDays(Carbon::now());
    }
}
