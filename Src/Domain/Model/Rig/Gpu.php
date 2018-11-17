<?php declare(strict_types=1);


namespace Src\Domain\Model\Rig;


use Src\Domain\Enum\GpuType;
use Src\Domain\Model\BaseModel;

class Gpu extends BaseModel
{
    /**
     * @var int
     */
    private $rigId = 0;
    
    /**
     * @var string
     */
    private $reference = '';
    
    /**
     * @var int
     */
    private $type = GpuType::__DEFAULT;
    
    /**
     * @var string
     */
    private $name = '';
    
    /**
     * @var int
     */
    private $bus = 0;
    
    /**
     * @var string
     */
    private $serial = '';
    
    /**
     * @var bool
     */
    private $active = false;
    
    /**
     * @return int
     */
    public function getRigId(): int
    {
        return $this->rigId;
    }
    
    /**
     * @param int $rigId
     */
    public function setRigId(int $rigId): void
    {
        $this->rigId = $rigId;
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
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }
    
    /**
     * @param int $type
     */
    public function setType(int $type): void
    {
        $this->type = $type;
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
     * @return int
     */
    public function getBus(): int
    {
        return $this->bus;
    }
    
    /**
     * @param int $bus
     */
    public function setBus(int $bus): void
    {
        $this->bus = $bus;
    }
    
    /**
     * @return string
     */
    public function getSerial(): string
    {
        return $this->serial;
    }
    
    /**
     * @param string $serial
     */
    public function setSerial(string $serial): void
    {
        $this->serial = $serial;
    }
    
    /**
     * @return bool
     */
    public function getActive(): bool
    {
        return (bool)$this->active;
    }
    
    /**
     * @param bool $active
     */
    public function setActive($active): void
    {
        $this->active = (bool)$active;
    }
}