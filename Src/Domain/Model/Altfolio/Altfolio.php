<?php declare(strict_types=1);


namespace Src\Domain\Model\Altfolio;


use Src\Domain\Model\BaseModel;

class Altfolio extends BaseModel
{
    /**
     * @var int
     */
    private $userId = null;
    
    /**
     * @var string
     */
    private $name = '';
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
    
    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }
    
    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }
}