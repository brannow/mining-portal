<?php declare(strict_types=1);


namespace Fuyukai\Userspace\Enum;


abstract class AbstractEnum
{
    private static $constCache = [];
    
    /**
     * @param bool $includeDefault
     * @return array
     */
    public static function getConstants(bool $includeDefault = false): array
    {
        $cacheKey = crc32(static::class);
        if (isset(self::$constCache[$cacheKey])) {
            $constants = self::$constCache[$cacheKey];
        } else {
            $ref = new \ReflectionClass(static::class);
            $constants = $ref->getConstants();
            self::$constCache[$cacheKey] = $constants;
        }
        
        if (!$includeDefault && array_key_exists('__DEFAULT', $constants)) {
            unset($constants['__DEFAULT']);
        }
        
        return $constants;
    }
    
    /**
     * @param $value
     * @return bool
     */
    public static function checkValue($value): bool
    {
        return in_array($value, self::getConstants(), true);
    }
}