<?php declare(strict_types=1);


namespace Fuyukai\Userspace\Secure;


abstract class PasswordHashing
{
    private const ALGO = PASSWORD_BCRYPT;
    
    /**
     * @param string $rawPassword
     * @return string
     */
    public static function hashPassword(string $rawPassword): string
    {
        return password_hash($rawPassword, self::ALGO);
    }
    
    /**
     * @param string $rawPassword
     * @param string $hash
     * @return bool
     */
    public static function validatePassword(string $rawPassword, string $hash): bool
    {
        return password_verify($rawPassword, $hash);
    }
}