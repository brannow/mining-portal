<?php declare(strict_types=1);


namespace Src\Frontend\Api\Service;


use Src\Domain\Enum\GpuType;
use Src\Domain\Model\Rig\Gpu;
use Src\Domain\Model\Rig\GpuTelemetry;
use Src\Domain\Model\Rig\Rig;
use Src\Domain\Model\Rig\RigTelemetry;
use Src\Domain\Repository\BaseRepository;
use Src\Domain\Repository\RigRepository;
use Src\Domain\Repository\UserRepository;

class RigService
{
    private const USER_KEY = 'user-key';
    private const RIG_GPUS = 'gpus';
    private const RIG_REFERENCE = 'identifier';
    private const RIG_NAME = 'name';
    private const RIG_OS_TYPE = 'os';
    
    private const RIG_ALGO = 'algo';
    private const RIG_CLIENT_UPTIME = 'client-uptime';
    private const RIG_ENV_TEMP = 'environment-temp';
    private const RIG_CPU_USAGE = 'cpu-usage';
    private const RIG_CPU_TEMP = 'cpu-temp';
    private const RIG_RAM_USAGE = 'ram-usage';
    private const RIG_HASH_RATE = 'khash-rate';
    private const RIG_POWER_USAGE = 'power';
    private const RIG_WATT_HOURS = 'kwh';
    
    private const RIG_GPU_REFERENCE = 'reference';
    private const RIG_GPU_TYPE = 'type';
    private const RIG_GPU_NAME = 'name';
    private const RIG_GPU_SERIAL = 'serial';
    private const RIG_GPU_BUS = 'bus';
    
    private const RIG_GPU_CORE_USAGE = 'core-usage';
    private const RIG_GPU_CORE_TEMP = 'core-temp';
    private const RIG_GPU_RAM_USAGE = 'ram-usage';
    private const RIG_GPU_RAM_TOTAL = 'ram-total';
    private const RIG_GPU_FAN_SPEED = 'fan';
    private const RIG_GPU_HASH_RATE = 'khash-rate';
    private const RIG_GPU_POWER_USAGE = 'power';
    private const RIG_GPU_ACTIVE = 'active';
    
    /**
     * @var RigRepository
     */
    private static $rigRepository = null;
    
    /**
     * @return RigRepository
     */
    private static function getRigRepository(): RigRepository
    {
        if (!static::$rigRepository) {
            static::$rigRepository = new RigRepository();
        }
        
        return static::$rigRepository;
    }
    
    /**
     * @param string $reference
     * @param int $userId
     * @param array $data
     * @return Rig
     */
    private static function getRigFromRigReferenceAndUserId(string $reference, int $userId, array $data): Rig
    {
        $rig = static::getRigRepository()->findRigFromReferenceAndUserId($reference, $userId);
        
        if (!$rig) {
            $rig = new Rig();
            $rig->setUserId($userId);
            $rig->setReference($reference);
            $rig->setOsType((int)$data[static::RIG_OS_TYPE]);
            
            if (!empty($data[static::RIG_NAME])) {
                $rig->setName((string)$data[static::RIG_NAME]);
            } else {
                $rig->setName(uniqid('Rig#'));
            }
    
            static::getRigRepository()->updateRig($rig);
        }
        
        return $rig;
    }
    
    /**
     * @param array $data
     * @param int $rigId
     * @return Gpu
     */
    private static function createGpuFromData(array $data, int $rigId): ?Gpu
    {
        $gpu = null;
        if ($rigId > 0 && !empty($data[static::RIG_GPU_REFERENCE])) {
            $gpu = new Gpu();
            $gpu->setRigId($rigId);
            $gpu->setActive((bool)$data[static::RIG_GPU_ACTIVE]);
            $gpu->setReference((string)$data[static::RIG_GPU_REFERENCE]);
    
            if (array_key_exists(static::RIG_GPU_TYPE, $data) && GpuType::checkValue($data[static::RIG_GPU_TYPE])) {
                $gpu->setType((int)$data[static::RIG_GPU_TYPE]);
            }
            if (!empty($data[static::RIG_GPU_NAME])) {
                $gpu->setName((string)$data[static::RIG_GPU_NAME]);
            }
            if (!empty($data[static::RIG_GPU_SERIAL])) {
                $gpu->setSerial((string)$data[static::RIG_GPU_SERIAL]);
            }
            if (!empty($data[static::RIG_GPU_BUS])) {
                $gpu->setBus((int)$data[static::RIG_GPU_BUS]);
            }

            static::getRigRepository()->updateGpu($gpu);
        }
        
        return $gpu;
    }
    
    /**
     * @param array $data
     * @param Rig $rig
     * @return null|RigTelemetry
     */
    private static function parseRigTelemetryData(array $data, Rig $rig): ?RigTelemetry
    {
        $telemetry = null;
        
        if ($rig->getId()) {
            $telemetry = new RigTelemetry();
            $telemetry->setRigId($rig->getId());
            
            if (isset($data[static::RIG_CLIENT_UPTIME])) {
                $telemetry->setClientUptime((int)$data[static::RIG_CLIENT_UPTIME]);
            }
            if (isset($data[static::RIG_ENV_TEMP])) {
                $telemetry->setEnvironmentTemp((float)$data[static::RIG_ENV_TEMP]);
            }
            if (isset($data[static::RIG_ALGO])) {
                $telemetry->setAlgo(strtolower(trim($data[static::RIG_ALGO])));
            }
            if (isset($data[static::RIG_CPU_USAGE])) {
                $telemetry->setCpuUsage((float)$data[static::RIG_CPU_USAGE]);
            }
            if (isset($data[static::RIG_CPU_TEMP])) {
                $telemetry->setCpuTemp((float)$data[static::RIG_CPU_TEMP]);
            }
            if (isset($data[static::RIG_RAM_USAGE])) {
                $telemetry->setRamUsage((float)$data[static::RIG_RAM_USAGE]);
            }
            if (isset($data[static::RIG_HASH_RATE])) {
                $telemetry->setHashRate((float)$data[static::RIG_HASH_RATE]);
            }
            if (isset($data[static::RIG_POWER_USAGE])) {
                $telemetry->setPower((float)$data[static::RIG_POWER_USAGE]);
            }
            if (isset($data[static::RIG_WATT_HOURS])) {
                $telemetry->setWattHours((float)$data[static::RIG_WATT_HOURS]);
            }
    
            static::getRigRepository()->updateRigTelemetry($telemetry);
        }
        
        return $telemetry;
    }
    
    /**
     * @param array $data
     * @param Gpu $gpu
     * @param string $gpuAlgo
     * @return null|GpuTelemetry
     */
    private static function parseGpuTelemetryData(array $data, Gpu $gpu, string $gpuAlgo = ''): ?GpuTelemetry
    {
        $telemetry = null;
    
        if ($gpu->getId()) {
            $telemetry = new GpuTelemetry();
            $telemetry->setGpuId($gpu->getId());
        
            if (!empty($gpuAlgo)) {
                $telemetry->setAlgo(strtolower(trim($gpuAlgo)));
            }
            if (isset($data[static::RIG_GPU_CORE_USAGE])) {
                $telemetry->setCoreUsage((int)$data[static::RIG_GPU_CORE_USAGE]);
            }
            if (isset($data[static::RIG_GPU_CORE_TEMP])) {
                $telemetry->setCoreTemp((int)$data[static::RIG_GPU_CORE_TEMP]);
            }
            if (isset($data[static::RIG_GPU_RAM_USAGE])) {
                $telemetry->setRamUsage((float)$data[static::RIG_GPU_RAM_USAGE]);
            }
            if (isset($data[static::RIG_GPU_RAM_TOTAL])) {
                $telemetry->setRamTotal((float)$data[static::RIG_GPU_RAM_TOTAL]);
            }
            if (isset($data[static::RIG_GPU_FAN_SPEED])) {
                $telemetry->setFanSpeed((float)$data[static::RIG_GPU_FAN_SPEED]);
            }
            if (isset($data[static::RIG_GPU_HASH_RATE])) {
                $telemetry->setHashRate((float)$data[static::RIG_GPU_HASH_RATE]);
            }
            if (isset($data[static::RIG_GPU_POWER_USAGE])) {
                $telemetry->setPower((float)$data[static::RIG_GPU_POWER_USAGE]);
            }
        }
    
        return $telemetry;
    }

    /**
     * @param string $key
     * @return int
     */
    private static function getUserIdFromRigKey(string $key): int
    {
        $userRepo = new UserRepository();
        return $userRepo->findUserIdFromRigKey($key);
    }
    
    /**
     * @param int $rigId
     * @return array
     */
    private static function getGpuListFromRigId(int $rigId): array
    {
        $gpus = static::getRigRepository()->findGpusWithRigid($rigId);
    
        $existingGpuList = [];
        /** @var Gpu $gpu */
        foreach ($gpus as $gpu) {
            if ($gpu->getReference() !== '') {
                $existingGpuList[$gpu->getReference()] = $gpu;
            }
        }
        
        return $existingGpuList;
    }
    
    /**
     * @param array $gpus
     */
    private static function deactivateLeagcyGpus(array $gpus)
    {
        /** @var Gpu $legacyGpu */
        foreach ($gpus as $legacyGpu) {
            $legacyGpu->setActive(false);
        }
    
        if ($gpus) {
            static::getRigRepository()->updateGpu(...array_values($gpus));
        }
        
    }
    
    /**
     * @param array $data
     * @param int $errorCode
     * @return bool
     */
    public static function saveRigTelemetry(array $data, int &$errorCode): bool
    {
        if (empty($data)) {
            $errorCode = 2;
            return false;
        }
    
        if (empty($data[static::USER_KEY])) {
            $errorCode = 3;
            return false;
        }
    
        if (empty($data[static::RIG_REFERENCE])) {
            $errorCode = 4;
            return false;
        }
    
    
        // find rig user
        $userId = static::getUserIdFromRigKey((string)$data[static::USER_KEY]);
        if ($userId) {
        
            $rig = static::getRigFromRigReferenceAndUserId($data[static::RIG_REFERENCE], $userId, $data);
        
            if ($rig && $rig->getId() > 0) {
            
                $rigTelemetry = static::parseRigTelemetryData($data, $rig);
                $gpuAlgo = $rigTelemetry->getAlgo();
                $existingGpuList = static::getGpuListFromRigId($rig->getId());
            
                $gpuTelemetries = [];
                foreach ($data[static::RIG_GPUS] as $gpuData) {
                
                    $gpu = null;
                    if (!empty($gpuData[static::RIG_GPU_REFERENCE]) && isset($existingGpuList[$gpuData[static::RIG_GPU_REFERENCE]])) {
                        /** @var Gpu $gpu */
                        $gpu = $existingGpuList[$gpuData[static::RIG_GPU_REFERENCE]];
                        if (isset($gpuData[static::RIG_GPU_ACTIVE])) {
                            $gpuActiveStatus = (bool)$gpuData[static::RIG_GPU_ACTIVE];
                            $gpu->setActive($gpuActiveStatus);
                        } else {
                            $gpu->setActive(false);
                        }
                        
                        if (!$gpu->getActive())
                        {
                            continue;
                        }
                        
                        // remove gpu from the lookupList
                        unset($existingGpuList[$gpuData[static::RIG_GPU_REFERENCE]]);
                    } else {
                        $gpu = static::createGpuFromData($gpuData, $rig->getId());
                    }
                
                    if ($gpu && $gpu->getId() > 0) {
                        if ($gpu->getActive()) {
                            $gpuTelemetry = static::parseGpuTelemetryData($gpuData, $gpu, $gpuAlgo);
                            if ($gpuTelemetry) {
                                $gpuTelemetries[] = $gpuTelemetry;
                            }
                        }
                    }
                }
            
                if ($gpuTelemetries) {
                    static::getRigRepository()->updateGpuTelemetry(...$gpuTelemetries);
                }
            
                // all remain GPUs are not in the system anymore ... deactivate it
                static::deactivateLeagcyGpus($existingGpuList);
            
                if (!$rigTelemetry) {
                    $errorCode = 8;
                }
                
                return (bool)$rigTelemetry;
            } else {
                // rig not found or not possible to create a new one
                $errorCode = 7;
            }
        } else {
            // user not found
            $errorCode = 6;
        }
        
        return false;
    }
}