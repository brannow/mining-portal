<?php declare(strict_types=1);


namespace Fuyukai\Userspace\Secure;


use Config\Config;

abstract class Encryption
{
    private const ALGO = 'aes-256-cbc';
    private const IV_ALGO = 'sha512';
    private const SIZE = 32;
    
    private const I_KEY = 'ek';
    private const I_VECTOR = 'iv';
    
    /**
     * @param string $userSalt
     * @return string
     */
    public static function generateEncryptionKey(string $userSalt): string
    {
        $ivSalt = openssl_random_pseudo_bytes(self::SIZE);
        $encryptionKey = openssl_random_pseudo_bytes(self::SIZE);
        return self::encodeKey($encryptionKey, self::createInitVector($ivSalt, $userSalt));
    }
    
    /**
     * @return string
     */
    public static function generateCSRFToken(): string
    {
        $ivSalt = openssl_random_pseudo_bytes(8);
        $vector = openssl_random_pseudo_bytes(self::SIZE);
        return hash_pbkdf2(self::IV_ALGO, $vector, $ivSalt, 1, 64, false);
    }
    
    /**
     * @param string $ivSalt
     * @param string $userSalt
     * @return string
     */
    private static function createInitVector(string $ivSalt, string $userSalt): string
    {
        return hash_pbkdf2(self::IV_ALGO, $ivSalt, $userSalt, 3, openssl_cipher_iv_length(self::ALGO), true);
    }
    
    /**
     * @param string $key
     * @param string $iv
     * @return string
     */
    private static function encodeKey(string $key, string $iv): string
    {
        if ($key && $iv) {
            // create junk
            $d1 = openssl_random_pseudo_bytes(self::SIZE);
            $d2 = openssl_random_pseudo_bytes(self::SIZE);
            $d3 = openssl_random_pseudo_bytes(self::SIZE);
            $tail = openssl_random_pseudo_bytes(3);
    
            return $d1 . $iv . $d2 . $d3 . $key . $tail;
        }
    }
    
    /**
     * @param string $payload
     * @return array
     */
    private static function decodeKey(string $payload): array
    {
        $keyData = [
            self::I_KEY => '',
            self::I_VECTOR => ''
        ];
        $byteChunks = str_split($payload, openssl_cipher_iv_length(self::ALGO));
        if (count($byteChunks) === 10) {
            $keyData[self::I_VECTOR] = $byteChunks[2];
            $keyData[self::I_KEY] = $byteChunks[7].$byteChunks[8];
        }
        
        return $keyData;
    }
    
    /**
     * @param string $key
     * @param string $data
     * @param string $salt
     * @return string
     */
    public static function encrypt(string $key, string $data, string $salt = ''): string
    {
        $keyData = self::decodeKey($key);
        if (!empty($keyData[self::I_KEY]) && !empty($keyData[self::I_VECTOR])) {
            return (string)openssl_encrypt(
                $data,
                self::ALGO,
                $keyData[self::I_KEY] . $salt,
                OPENSSL_RAW_DATA,
                self::createInitVector($keyData[self::I_VECTOR], $salt)
            );
        }
        
        return '';
    }
    
    /**
     * @param string $key
     * @param string $payload
     * @param string $salt
     * @return string
     */
    public static function decrypt(string $key, string $payload, string $salt = ''): string
    {
        $keyData = self::decodeKey($key);
        if (!empty($keyData[self::I_KEY]) && !empty($keyData[self::I_VECTOR])) {
             return (string)openssl_decrypt(
                 $payload,
                 self::ALGO,
                 $keyData[self::I_KEY],
                 OPENSSL_RAW_DATA,
                 self::createInitVector($keyData[self::I_VECTOR], $salt)
             );
        }
        
        return '';
    }
    
    /**
     * @param string $data
     * @param string $salt
     * @return string
     */
    public static function systemEncrypt(string $data, string $salt = ''): string
    {
        return self::encrypt(hex2bin(Config::getConfigEntry(Config::SYSTEM_KEY)), $data, $salt);
    }
    
    /**
     * @param string $payload
     * @param string $salt
     * @return string
     */
    public static function systemDecrypt(string $payload, string $salt = ''): string
    {
        return self::decrypt(hex2bin(Config::getConfigEntry(Config::SYSTEM_KEY)), $payload, $salt);
    }
}