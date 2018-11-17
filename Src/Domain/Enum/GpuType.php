<?php declare(strict_types=1);


namespace Src\Domain\Enum;


use Fuyukai\Userspace\Enum\AbstractEnum;

abstract class GpuType extends AbstractEnum
{
    public const __DEFAULT = self::Generic;
    
    public const Generic = 0;
    public const NVIDIA = 1;
    public const AIT = 2;
    
    private static $vendorNameMap = [
        self::Generic => 'generic',
        self::NVIDIA => 'nvidia',
        self::AIT => 'ati'
    ];
    
    /**
     * @param int $type
     * @return string
     */
    public static function getVendorName(int $type): string
    {
        if (isset(self::$vendorNameMap[$type])) {
            return self::$vendorNameMap[$type];
        }
        
        return '';
    }
}