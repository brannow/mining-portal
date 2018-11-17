<?php declare(strict_types=1);


namespace Src\Domain\Model\Rig;


use Src\Domain\Model\BaseModel;

class GpuTelemetry extends BaseModel
{
    /**
     * @var int
     */
    private $gpuId = 0;
    
    /**
     * @var string
     */
    private $algo = '';
    
    /**
     * @var float
     */
    private $coreTemp = 0.0;
    
    /**
     * @var float
     */
    private $coreUsage = 0.0;
    
    /**
     * @var float
     */
    private $ramUsage = 0.0;
    
    /**
     * @var float
     */
    private $ramTotal = 0.0;
    
    /**
     * @var float
     */
    private $fanSpeed = 0.0;
    
    /**
     * @var float
     */
    private $hashRate = 0.0;
    
    /**
     * @var float
     */
    private $power = 0.0;
    
    /**
     * @var string
     */
    private $created = '';
    
    /**
     * @return int
     */
    public function getGpuId(): int
    {
        return $this->gpuId;
    }
    
    /**
     * @param int $gpuId
     */
    public function setGpuId(int $gpuId): void
    {
        $this->gpuId = $gpuId;
    }
    
    /**
     * @return string
     */
    public function getAlgo(): string
    {
        return $this->algo;
    }
    
    /**
     * @param string $algo
     */
    public function setAlgo(string $algo): void
    {
        $this->algo = $algo;
    }
    
    /**
     * @return float
     */
    public function getCoreTemp(): float
    {
        return $this->coreTemp;
    }
    
    /**
     * @param float $coreTemp
     */
    public function setCoreTemp(float $coreTemp): void
    {
        $this->coreTemp = $coreTemp;
    }
    
    /**
     * @return float
     */
    public function getCoreUsage(): float
    {
        return $this->coreUsage;
    }
    
    /**
     * @param float $coreUsage
     */
    public function setCoreUsage(float $coreUsage): void
    {
        $this->coreUsage = $coreUsage;
    }
    
    /**
     * @return float
     */
    public function getRamUsage(): float
    {
        return $this->ramUsage;
    }
    
    /**
     * @param float $ramUsage
     */
    public function setRamUsage(float $ramUsage): void
    {
        $this->ramUsage = $ramUsage;
    }
    
    /**
     * @return float
     */
    public function getRamTotal(): float
    {
        return $this->ramTotal;
    }
    
    /**
     * @param float $ramTotal
     */
    public function setRamTotal(float $ramTotal): void
    {
        $this->ramTotal = $ramTotal;
    }
    
    /**
     * @return float
     */
    public function getFanSpeed(): float
    {
        return $this->fanSpeed;
    }
    
    /**
     * @param float $fanSpeed
     */
    public function setFanSpeed(float $fanSpeed): void
    {
        $this->fanSpeed = $fanSpeed;
    }
    
    /**
     * @return float
     */
    public function getHashRate(): float
    {
        return $this->hashRate;
    }
    
    /**
     * @param float $hashRate
     */
    public function setHashRate(float $hashRate): void
    {
        $this->hashRate = $hashRate;
    }
    
    /**
     * @return string
     */
    public function hashRateString(): string
    {
        $hashRate = $this->getHashRate();
        if ($hashRate > 1000000000) {
            return number_format(round($hashRate / 1000000000, 2), 2). ' TH/s';
        } elseif ($hashRate > 1000000) {
            return number_format(round($hashRate / 1000000, 2), 2). ' GH/s';
        } elseif ($hashRate > 1000) {
            return number_format(round($hashRate / 1000, 2), 2). ' MH/s';
        } elseif ($hashRate >= 1) {
            return number_format(round($hashRate, 2), 2). ' KH/s';
        } elseif ($hashRate < 1 && $hashRate > 0) {
            return round($hashRate * 1000, 2). ' H/s';
        }
        
        return '0 H/s';
    }
    
    /**
     * @return float
     */
    public function getPower(): float
    {
        return $this->power;
    }
    
    /**
     * @param float $power
     */
    public function setPower(float $power): void
    {
        $this->power = $power;
    }
    
    /**
     * @return string
     */
    public function getCreated(): string
    {
        return $this->created;
    }
    
    /**
     * @param string $created
     */
    public function setCreated(string $created): void
    {
        $this->created = $created;
    }
    
    /**
     * @return int
     */
    public function timeDiffSinceUpdate(): int
    {
        return (int)round((time() - strtotime($this->getCreated())) / 60, 0);
    }
}