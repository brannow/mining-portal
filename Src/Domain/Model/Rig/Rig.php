<?php declare(strict_types=1);


namespace Src\Domain\Model\Rig;


use Src\Domain\Enum\RigOsType;
use Src\Domain\Model\BaseModel;

class Rig extends BaseModel
{
    /**
     * @var int
     */
    private $userId = 0;
    
    /**
     * @var string
     */
    private $reference = '';
    
    /**
     * @var string
     */
    private $name = '';
    
    /**
     * @var string
     */
    private $location = '';
    
    /**
     * @var float
     */
    private $price = 0.0;
    
    /**
     * @var int
     */
    private $osType = RigOsType::__DEFAULT;
    
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
    
    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }
    
    /**
     * @param string $reference
     */
    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }
    
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
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }
    
    /**
     * @param string $location
     */
    public function setLocation(string $location): void
    {
        $this->location = $location;
    }
    
    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }
    
    /**
     * @param float $price
     */
    public function setPrice(float $price): void
    {
        $this->price = $price;
    }
    
    /**
     * @return int
     */
    public function getOsType(): int
    {
        return $this->osType;
    }
    
    /**
     * @param int $osType
     */
    public function setOsType(int $osType): void
    {
        $this->osType = $osType;
    }
}