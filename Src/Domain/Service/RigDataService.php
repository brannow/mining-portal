<?php declare(strict_types=1);


namespace Src\Domain\Service;

use Src\Domain\Enum\GpuType;
use Src\Domain\Enum\RigOsType;
use Src\Domain\Model\Rig\Gpu;
use Src\Domain\Model\Rig\Rig;
use Src\Domain\Model\User;

class RigDataService extends BaseService
{
    public const RIG_ID = 'id';
    public const RIG_REFERENCE = 'ref';
    public const RIG_STATUS = 'status';
    public const RIG_OS = 'os';
    public const RIG_NAME = 'name';
    public const RIG_UPTIME_CLIENT = 'uptime_client';
    public const RIG_ENVIRONMENT_TEMP = 'env_temp';
    public const RIG_CPU_USAGE = 'cpu_usage';
    public const RIG_CPU_TEMP = 'cpu_temp';
    public const RIG_RAM_USAGE = 'ram_usage';
    public const RIG_HASH_RATE = 'hash_rate';
    public const RIG_POWER = 'power';
    public const RIG_WATT_HOURS = 'kwh';
    public const RIG_CREATED = 'created';
    public const RIG_LAST_UPDATE_INTERVAL = 'last_update';
    
    public const RIG_GPU = 'gpu';
    public const RIG_GPU_TYPE = 'gpu_type';
    public const RIG_GPU_ID = 'gpu_id';
    public const RIG_GPU_REFERENCE = 'gpu_ref';
    public const RIG_GPU_NAME = 'gpu_name';
    public const RIG_GPU_BUS = 'gpu_bus';
    public const RIG_GPU_SERIAL = 'gpu_serial';
    
    public const RIG_GPU_TEMP = 'gpu_temp';
    public const RIG_GPU_USAGE = 'gpu_usage';
    public const RIG_GPU_RAM_USAGE = 'gpu_ram_usage';
    public const RIG_GPU_RAM_TOTAL = 'gpu_ram_total';
    public const RIG_GPU_FAN = 'gpu_fan';
    public const RIG_GPU_HASH_RATE = 'gpu_hash_rate';
    public const RIG_GPU_POWER = 'gpu_power';
    public const RIG_GPU_CREATED = 'gpu_created';
    public const RIG_GPU_LAST_UPDATE_INTERVAL = 'gpu_last_update';
    
    
    /**
     * @var User
     */
    private $user = null;
    
    /**
     * RigService constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    
    /**
     * @return array
     */
    public function getRigBaseTelemetry(): array
    {
        $rigs = $this->getRigRepository()->findRigsWithUserId($this->user->getId());
        $rigData = [];
        /** @var Rig $rig */
        foreach ($rigs as $rig) {
            $rigInfo = $this->getRigBaseTelemetryForRig($rig);
            if ($rigInfo) {
                $rigData[] = $rigInfo;
            }
            
        }
        
        return $rigData;
    }
    
    /**
     * @return array
     */
    public function getRigAdvancedTelemetry(): array
    {
        $rigs = $this->getRigRepository()->findRigsWithUserId($this->user->getId());
        $rigData = [];
        /** @var Rig $rig */
        foreach ($rigs as $rig) {
            $rigBaseData = $this->getRigBaseTelemetryForRig($rig);
            if ($rigBaseData) {
                $rigBaseData[static::RIG_GPU] = $this->getRigGpuBaseTelemetry($rig);
                $rigData[] = $rigBaseData;
            }
        }
    
        return $rigData;
    }
    
    /**
     * @param Rig $rig
     * @return array
     */
    public function getRigGpuBaseTelemetry(Rig $rig): array
    {
        return $this->getRigGpuBaseTelemetryForRigId($rig->getId());
    }
    
    /**
     * @param int $rigId
     * @return array
     */
    public function getRigGpuBaseTelemetryForRigId(int $rigId): array
    {
        $gpus =$this->getRigRepository()->findGpusWithRigid($rigId);
        $gpuData = [];
        foreach ($gpus as $gpu) {
            $gpuInfo = $this->getRigBaseTelemetryForGpu($gpu);
            if ($gpuInfo) {
                $gpuData[] = $gpuInfo;
            }
        }
        
        return $gpuData;
    }
    
    /**
     * @param int $rigId
     * @return array
     */
    public function getRigBaseTelemetryForRigId(int $rigId): array
    {
        $rig = $this->getRigRepository()->findRigsWithRigId($rigId);
        
        if ($rig) {
            return $this->getRigBaseTelemetryForRig($rig);
        }
        
        return [];
    }
    
    /**
     * @param Rig $rig
     * @return array
     */
    public function getRigBaseTelemetryForRig(Rig $rig): array
    {
        $telemetry = $this->getRigRepository()->findLatestTelemetryWithRigId($rig->getId());
        if ($telemetry) {
            return [
                static::RIG_ID => $rig->getId(),
                static::RIG_REFERENCE => $rig->getReference(),
                static::RIG_STATUS => $telemetry->status(),
                static::RIG_OS => RigOsType::getOSName($rig->getOsType()),
                static::RIG_NAME => $rig->getName(),
                static::RIG_ENVIRONMENT_TEMP => $telemetry->getEnvironmentTemp(),
                static::RIG_UPTIME_CLIENT => $telemetry->clientUptimeString(),
                static::RIG_CPU_USAGE => round($telemetry->getCpuUsage(), 0),
                static::RIG_CPU_TEMP => round($telemetry->getCpuTemp(), 0),
                static::RIG_RAM_USAGE => round($telemetry->getRamUsage(), 0),
                static::RIG_HASH_RATE => $telemetry->hashRateString(),
                static::RIG_POWER => round($telemetry->getPower(), 0),
                static::RIG_WATT_HOURS => round($telemetry->getWattHours(), 1),
                static::RIG_CREATED => $telemetry->getCreated(),
                static::RIG_LAST_UPDATE_INTERVAL => $telemetry->timeDiffSinceUpdate()
            ];
        }
        
        return [];
    }
    
    /**
     * @param Gpu $gpu
     * @return array
     */
    public function getRigBaseTelemetryForGpu(Gpu $gpu): array
    {
        $telemetry = $this->getRigRepository()->findLatestTelemetryWithGpuId($gpu->getId());
        if ($telemetry) {
            return [
                static::RIG_GPU_ID => $gpu->getId(),
                static::RIG_GPU_REFERENCE => $gpu->getReference(),
                static::RIG_GPU_BUS => $gpu->getBus(),
                static::RIG_GPU_TYPE => GpuType::getVendorName($gpu->getType()),
                static::RIG_GPU_NAME => $gpu->getName(),
                static::RIG_GPU_SERIAL => $gpu->getSerial(),
                static::RIG_GPU_TEMP => round($telemetry->getCoreTemp(), 0),
                static::RIG_GPU_USAGE => round($telemetry->getCoreUsage(), 0),
                static::RIG_GPU_RAM_USAGE => round($telemetry->getRamUsage(), 0),
                static::RIG_GPU_RAM_TOTAL => round($telemetry->getRamTotal(), 0),
                static::RIG_GPU_FAN => round($telemetry->getFanSpeed(), 0),
                static::RIG_GPU_HASH_RATE => $telemetry->hashRateString(),
                static::RIG_GPU_POWER => round($telemetry->getPower(), 0),
                static::RIG_GPU_CREATED => $telemetry->getCreated(),
                static::RIG_GPU_LAST_UPDATE_INTERVAL => $telemetry->timeDiffSinceUpdate()
            ];
        }
        
        return [];
    }
}