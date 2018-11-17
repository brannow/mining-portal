<?php declare(strict_types=1);


namespace Fuyukai\Userspace\Service;


use Fuyukai\Userspace\Secure\Encryption;
use Fuyukai\Userspace\Secure\PasswordHashing;
use Fuyukai\Userspace\Session\SessionHandler;
use Src\Domain\Enum\StatusCodes;
use Src\Domain\Enum\UserLevel;
use Src\Domain\Model\User;
use Src\Domain\Repository\UserRepository;

class LoginService
{
    // in microseconds
    private const TIMEOUT = 500000;
    
    /**
     * @var SessionHandler
     */
    private $session = null;
    
    /**
     * @var UserRepository
     */
    private $userRepository = null;
    
    /**
     * @var string
     */
    private $spoofProtectionRequestKey = '';
    
    /**
     * @var int
     */
    private $error = StatusCodes::STATUS_NONE;
    
    /**
     *
     */
    public function init()
    {
        $this->getSession()->init();
    }
    
    /**
     * @param string $spoofKey
     */
    public function setSpoofProtectionRequestKey(string $spoofKey)
    {
        $this->spoofProtectionRequestKey = $spoofKey;
    }
    
    /**
     * @return null|User
     */
    public function recoverUserFromSession(): ?User
    {
        $userId = (int)$this->getSession()->getValue('id', 'user');
        if ($userId) {
            if (!$this->isSessionSpoofed()) {
                if (!$this->getSession()->isExpired()) {
                    $user = $this->getUserRepository()->findUserById($userId);
                    
                    if ($user) {
                        if ($user->getLevel() !== UserLevel::LOCKED) {
                            $this->getSession()->updateExpireTimeout();
                            return $user;
                        }
                    }
                } else {
                    $this->error = StatusCodes::STATUS_LOGIN_EXPIRED;
                }
            }
            
            // wipe session if session data exist but they are invalid or expired
            $this->getSession()->wipe();
        }
        
        return null;
    }
    
    /**
     * @return bool
     */
    private function isSessionSpoofed(): bool
    {
        $spoofProtection = $this->getSession()->getValue('spoofProtection', 'user');
        if ($spoofProtection) {
            
            // md5 HTTP_X_FORWARDED_FOR + REMOTE_ADDR
            return !($spoofProtection === $this->spoofProtectionRequestKey);
        }
        
        return true;
    }
    
    /**
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function tryCreateUserSession(string $username, string $password): bool
    {
        $timingAttackPrevent = microtime(true);
        
        if (!empty($username) && !empty($password)) {
            $userHashData = $this->getUserRepository()->findUserHashByUsername($username);
            if ($userHashData && !empty($userHashData['id']) && !empty($userHashData['password'])) {
                $userId = (int)$userHashData['id'];
                $passwordHash = $userHashData['password'];
                if (PasswordHashing::validatePassword($password, $passwordHash)) {
                    unset($password);
                    if ($this->createUserSession($userId)) {
                        return true;
                    } else {
                        $this->error = StatusCodes::STATUS_LOGIN_INTERNAL_ERROR;
                    }
                } else {
                    $this->error = StatusCodes::STATUS_LOGIN_PASSWORD_MISMATCH;
                }
            } else {
                $this->error = StatusCodes::STATUS_LOGIN_USERNAME_MISMATCH;
            }
        } else {
            $this->error = StatusCodes::STATUS_LOGIN_INVALID_CREDENTIALS;
        }


        // timing attack protection - every fail request ends in a sleep
        $timingAttackPrevent = (microtime(true) - $timingAttackPrevent) * static::TIMEOUT;
        usleep((int)(static::TIMEOUT - $timingAttackPrevent));
        
        return false;
    }
    
    /**
     * @param int $userId
     * @return bool
     */
    public function createUserSession(int $userId): bool
    {
        if ($this->spoofProtectionRequestKey && $userId > 0) {
            $this->getSession()->updateExpireTimeout();
            $this->getSession()->setValue((string)$userId ,'id', 'user');
            $this->getSession()->setValue($this->spoofProtectionRequestKey,'spoofProtection', 'user');
            
            return true;
        }
        
        return false;
    }
    
    /**
     *
     */
    public function destroySession()
    {
        $this->getSession()->wipe();
    }
    
    /**
     * @param string $requestedToken
     * @return bool
     */
    public function validateCSRFToken(string $requestedToken): bool
    {
        $sessionToken = $this->getSession()->getValue( 'csrf', 'login');
        $this->getSession()->setValue('', 'csrf', 'login');
        
        return (strlen($requestedToken) === 64 && $requestedToken === $sessionToken);
    }
    
    /**
     * @return string
     */
    public function generateCSRFToken(): string
    {
        // generates 64 byte (char[64]) token
        $newToken = Encryption::generateCSRFToken();
        $this->getSession()->setValue($newToken, 'csrf', 'login');
        return $newToken;
    }
    
    /**
     * @return SessionHandler
     */
    private function getSession(): SessionHandler
    {
        if (!$this->session) {
            $this->session = new SessionHandler();
        }
        return $this->session;
    }
    
    /**
     * @return UserRepository
     */
    private function getUserRepository(): UserRepository
    {
        if (!$this->userRepository) {
            $this->userRepository = new UserRepository();
        }
        return $this->userRepository;
    }
    
    /**
     * @return int
     */
    public function getErrorCode(): int
    {
        return $this->error;
    }
}