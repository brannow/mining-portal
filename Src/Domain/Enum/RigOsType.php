<?php declare(strict_types=1);


namespace Src\Domain\Enum;


use Fuyukai\Userspace\Enum\AbstractEnum;

abstract class RigOsType extends AbstractEnum
{
    public const __DEFAULT = self::WINDOWS;
    
    public const GENERIC = 0;
    public const WINDOWS = 1;
    public const LINUX = 2;
    public const MAC = 3;
    
    private static $osNameMap = [
        self::GENERIC => 'generic',
        self::WINDOWS => 'windows',
        self::LINUX => 'linux',
        self::MAC => 'mac'
    ];
    
    /**
     * @param int $type
     * @return string
     */
    public static function getOSName(int $type): string
    {
        if (isset(self::$osNameMap[$type])) {
            return self::$osNameMap[$type];
        }
        
        return '';
    }
}