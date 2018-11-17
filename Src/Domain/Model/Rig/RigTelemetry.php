<?php declare(strict_types=1);


namespace Src\Domain\Model\Rig;


use Src\Domain\Model\BaseModel;

class RigTelemetry extends BaseModel
{
    /**
     * Timeouts for the the Rig Status in Seconds
     */
    private const NOTICE_TIMEOUT = 1500;
    private const ERROR_TIMEOUT = 3600;
    
    /**
     * Rig Status
     */
    public const STATUS_ERROR = 0; // Telemetry is older than 1 Hour
    public const STATUS_OK = 1; // everything seems fine
    public const STATUS_NOTICE = 2; // Telemetry is older than 25min
    public const STATUS_WARNING = 3; // no Hashrate - miner has stopped / system is in Idle
    
    /**
     * @var int
     */
    private $rigId = 0;
    
    /**
     * @var string
     */
    private $algo = '';
    
    /**
     * @var float
     */
    private $environmentTemp = 0.0;
    
    /**
     * @var int
     */
    private $clientUptime = 0;
    
    /**
     * @var float
     */
    private $cpuUsage = 0.0;
    
    /**
     * @var float
     */
    private $cpuTemp = 0.0;
    
    /**
     * @var float
     */
    private $ramUsage = 0.0;
    
    /**
     * @var float
     */
    private $hashRate = 0.0;
    
    /**
     * @var float
     */
    private $power = 0.0;
    
    /**
     * @var float
     */
    private $wattHours = 0.0;
    
    /**
     * @var string
     */
    private $created = '';
    
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
     * @return int
     */
    public function getClientUptime(): int
    {
        return $this->clientUptime;
    }
    
    /**
     * @param int $clientUptime
     */
    public function setClientUptime(int $clientUptime): void
    {
        $this->clientUptime = $clientUptime;
    }
    
    /**
     * @return string
     */
    public function clientUptimeString(): string
    {
        return $this->upTimeString($this->getClientUptime());
    }

    /**
     * @return float
     */
    public function getCpuUsage(): float
    {
        return $this->cpuUsage;
    }
    
    /**
     * @param float $cpuUsage
     */
    public function setCpuUsage(float $cpuUsage): void
    {
        $this->cpuUsage = $cpuUsage;
    }
    
    /**
     * @return float
     */
    public function getCpuTemp(): float
    {
        return $this->cpuTemp;
    }
    
    /**
     * @param float $cpuTemp
     */
    public function setCpuTemp(float $cpuTemp): void
    {
        $this->cpuTemp = $cpuTemp;
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
     * in KiloHashes
     *
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
     * @return float
     */
    public function getWattHours(): float
    {
        return $this->wattHours;
    }
    
    /**
     * @param float $wattHours
     */
    public function setWattHours(float $wattHours): void
    {
        $this->wattHours = $wattHours;
    }
    
    /**
     * @return float
     */
    public function getEnvironmentTemp(): float
    {
        return $this->environmentTemp;
    }
    
    /**
     * @param float $environmentTemp
     */
    public function setEnvironmentTemp(float $environmentTemp): void
    {
        $this->environmentTemp = $environmentTemp;
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
     * Get the current rig status based on this telemetry data
     *
     * @return int
     */
    public function status(): int
    {
        $hashRate = $this->getHashRate();
        $telemetryTime = strtotime($this->getCreated());
        if ($hashRate > 0 && $telemetryTime > (time() - self::NOTICE_TIMEOUT)) {
            return static::STATUS_OK;
        }
    
        $fatalTime = (time() - self::ERROR_TIMEOUT);
        if ($hashRate === 0 && $telemetryTime > $fatalTime) {
            return static::STATUS_NOTICE;
        }
    
        if ($telemetryTime > $fatalTime) {
            return static::STATUS_NOTICE;
        }
    
        return static::STATUS_ERROR;
    }
    
    /**
     * @param int $minutes
     * @return string
     */
    private function upTimeString(int $minutes): string
    {
        if ($minutes < 60) {
            return $minutes.' min';
        } elseif ($minutes >= 60  && $minutes < 1440) {
            $hours = $minutes / 60;
            $hourInt = (int)floor($hours);
            $minutes = 60 * ($hours - $hourInt);
            if ($hourInt === 0) {
                return $hourInt . ' hours';
            }
            return $hourInt . ' hours ' . $minutes.' min';
        } elseif ($minutes > 1440) {
            $days = $minutes / 1440;
            $daysInt = (int)floor($days);
            $minutes = 1440 * ($days - $daysInt);
            $hours = $minutes / 60;
            $hourInt = (int)floor($hours);
            if ($hourInt === 0) {
                return $daysInt . ' days';
            }
            return $daysInt . ' days ' . $hourInt . ' hours';
        }
        
        return '---';
    }
    
    /**
     * @return int
     */
    public function timeDiffSinceUpdate(): int
    {
        return (int)round((time() - strtotime($this->getCreated())) / 60, 0);
    }
}