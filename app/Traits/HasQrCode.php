<?php
namespace App\Traits;

use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

trait HasQrCode
{
    protected static function bootHasQrCode()
    {
        static::creating(function ($model) {
            $model->qr_code = $model->qr_code ?? Str::uuid();
        });
    }

    public function generateQrCode(): string
    {
        return QrCode::size(300)
            ->format('svg')
            ->generate(config('app.url') . "/equipment/{$this->qr_code}");
    }

    public function getPublicUrl(): string
    {
        return url("/equipment/{$this->qr_code}");
    }

    public function getMaintenanceReportUrl(): string
    {
        return url("/equipment/{$this->qr_code}/report");
    }

    public function getQrCodePath(): string
    {
        return storage_path("app/public/qrcodes/{$this->qr_code}.svg");
    }

    public function regenerateQrCode(): void
    {
        $this->update(['qr_code' => Str::uuid()]);
    }
}
