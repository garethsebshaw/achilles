<?php
namespace App\Traits;

trait HandlesFileTypeIcons
{
    protected function renderFileTypeIcon($fileType)
    {
        $iconMap = [
            'pdf' => '<i class="fa fa-file-pdf text-black-500"></i>',
            'doc' => '<i class="fa fa-file-word text-black-500"></i>',
            'docx' => '<i class="fa fa-file-word text-black-500"></i>',
            'xls' => '<i class="fa fa-file-excel text-black-500"></i>',
            'xlsx' => '<i class="fa fa-file-excel text-black-500"></i>',
            'jpg' => '<i class="fa fa-file-image text-black-500-500"></i>',
            'jpeg' => '<i class="fa fa-file-image text-black-500"></i>',
            'png' => '<i class="fa fa-file-image text-black-500"></i>',
            'txt' => '<i class="fa fa-file-alt text-black-500"></i>',
            'zip' => '<i class="fa fa-file-archive text-black-500"></i>',
            'default' => '<i class="fa fa-file text-black-500"></i>'
        ];

        $fileType = strtolower($fileType);
        return $iconMap[$fileType] ?? $iconMap['default'];
    }
}
