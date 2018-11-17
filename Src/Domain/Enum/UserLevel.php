<?php declare(strict_types=1);


namespace Src\Domain\Enum;


use Fuyukai\Userspace\Enum\AbstractEnum;

abstract class UserLevel extends AbstractEnum
{
    public const __DEFAULT = self::LOCKED;
    
    public const LOCKED = 0;
    public const READ_ONLY = 1;
    public const USER = 2;
    public const OPERATOR = 3;
    public const ADMIN = 99;
    
    
    private static $levelNameMap = [
        self::LOCKED => 'Locked',
        self::READ_ONLY => 'Read-Only',
        self::USER => 'User',
        self::OPERATOR => 'Operator',
        self::ADMIN => 'Administrator',
    ];
    
    /**
     * @param int $level
     * @return string
     */
    public static function getLevelName(int $level): string
    {
        if (self::$levelNameMap[$level]) {
            return self::$levelNameMap[$level];
        }
        
        return '';
    }
}