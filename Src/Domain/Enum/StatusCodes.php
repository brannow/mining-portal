<?php declare(strict_types=1);


namespace Src\Domain\Enum;


use Fuyukai\Userspace\Enum\AbstractEnum;

abstract class StatusCodes extends AbstractEnum
{
    public const __DEFAULT = self::STATUS_NONE;
    
    public const STATUS_NONE = 0;
    public const STATUS_LOGIN_LOCKED = 2;
    public const STATUS_LOGIN_INTERNAL_ERROR = 3;
    public const STATUS_LOGIN_PASSWORD_MISMATCH = 4;
    public const STATUS_LOGIN_USERNAME_MISMATCH = 5;
    public const STATUS_LOGIN_INVALID_CREDENTIALS = 6;
    public const STATUS_LOGIN_EXPIRED = 7;
    
    private static $messageMap = [
        self::STATUS_LOGIN_LOCKED => 'Account is disabled',
        self::STATUS_LOGIN_INTERNAL_ERROR => 'Internal Server Error',
        self::STATUS_LOGIN_PASSWORD_MISMATCH => 'Invalid login credentials',
        self::STATUS_LOGIN_USERNAME_MISMATCH => 'Invalid login credentials',
        self::STATUS_LOGIN_INVALID_CREDENTIALS => 'Invalid login credentials',
        self::STATUS_LOGIN_EXPIRED => 'Session expired',
    ];
    
    /**
     * @param int $code
     * @return string
     */
    public static function statusCodeToMessage(int $code): string
    {
        $message = '';
        if (self::checkValue($code) && !empty(self::$messageMap[$code])) {
            $message = self::$messageMap[$code];
        }
        
        return $message;
    }
}