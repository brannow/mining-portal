<?php declare(strict_types=1);


namespace Src\Domain\Model;


use Src\Domain\Enum\UserLevel;

class User extends BaseModel
{
    /**
     * @var int
     */
    private $level = UserLevel::__DEFAULT;
    
    /**
     * @var string
     */
    private $email = '';
    
    /**
     * @var string
     */
    private $username = '';
    
    /**
     * @var string
     */
    private $password = '';
    
    /**
     * binary data
     * @var null|string
     */
    private $encryptionKey = null;
    
    /**
     * @var string
     */
    private $appToken = '';
    
    /**
     * @var string
     */
    private $rigKey = '';
    
    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }
    
    /**
     * @param int $level
     */
    public function setLevel(int $level): void
    {
        if (UserLevel::checkValue($level)) {
            $this->level = $level;
        }
    }
    
    public function levelName(): string
    {
        return UserLevel::getLevelName($this->getLevel());
    }
    
    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
    
    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
    
    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }
    
    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }
    
    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
    
    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
    
    /**
     * @return null|string
     */
    public function getEncryptionKey(): ?string
    {
        return $this->encryptionKey;
    }
    
    /**
     * @param null|string $encryptionKey
     */
    public function setEncryptionKey(?string $encryptionKey): void
    {
        if (!$encryptionKey) {
            $encryptionKey = null;
        }
        $this->encryptionKey = $encryptionKey;
    }
    
    /**
     * @return string
     */
    public function getAppToken(): string
    {
        return $this->appToken;
    }
    
    /**
     * @param string $appToken
     */
    public function setAppToken(string $appToken): void
    {
        $this->appToken = $appToken;
    }
    
    /**
     * @return string
     */
    public function getRigKey(): string
    {
        return $this->rigKey;
    }
    
    /**
     * @param string $rigKey
     */
    public function setRigKey(string $rigKey): void
    {
        $this->rigKey = $rigKey;
    }
}