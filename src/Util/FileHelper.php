<?php


namespace Refiler\Util;


class FileHelper
{
    private const  SIZE_STEPS = [
        'B', 'KB', 'MB', 'GB', 'TB',
    ];
    public static function getAppropriateSizeFormat(int $size) : string {
        if ($size < 1024) {
            return $size.self::SIZE_STEPS[0];
        }
        $currentStep = 0;
        while (($size = $size / 1024) >= 1024) {
            $currentStep++;
        }
        return  floor($size).static::SIZE_STEPS[$currentStep + 1];
    }
}