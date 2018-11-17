<?php declare(strict_types=1);


namespace Src\Domain\Repository;


use Src\Domain\Model\Rig\Gpu;
use Src\Domain\Model\Rig\GpuTelemetry;
use Src\Domain\Model\Rig\Rig;
use Src\Domain\Model\Rig\RigTelemetry;

class RigRepository extends BaseRepository
{
    /**
     * @param string $reference
     * @param int $userId
     * @return null|Rig
     */
    public function findRigFromReferenceAndUserId(string $reference, int $userId): ?Rig
    {
        $rigs = $this->selectModel(
            Rig::class,
            "rig",
            [],
            [
                'reference' => $reference,
                'user_id' => $userId
            ],
            [],
            1);
        if ($rigs) {
            
            return $rigs[0];
        }
        
        return null;
    }
    
    /**
     * @param int $userId
     * @return array
     */
    public function findRigsWithUserId(int $userId): array
    {
        return $this->selectModel(
            Rig::class,
            "rig",
            [],
            [
                'user_id' => $userId
            ]);
    }
    
    /**
     * @param int $id
     * @return null|Rig
     */
    public function findRigsWithRigId(int $id): ?Rig
    {
        $rigs = $this->selectModel(
            Rig::class,
            "rig",
            [],
            [
                'id' => $id
            ],
            [],
            1);
        
        if ($rigs) {
            return $rigs[0];
        }
        
        return null;
    }
    
    /**
     * @param int $rigId
     * @return null|RigTelemetry
     */
    public function findLatestTelemetryWithRigId(int $rigId): ?RigTelemetry
    {
        $telemetry = $this->selectModel(
            RigTelemetry::class,
            "rig_telemetry",
            [],
            [
                'rig_id' => $rigId
            ],
            ['id' => 'desc'],
            1);
        
        if ($telemetry) {
            return $telemetry[0];
        }
        
        return null;
    }
    
    /**
     * @param int $gpuId
     * @return null|GpuTelemetry
     */
    public function findLatestTelemetryWithGpuId(int $gpuId): ?GpuTelemetry
    {
        $telemetry = $this->selectModel(
            GpuTelemetry::class,
            "gpu_telemetry",
            [],
            [
                'gpu_id' => $gpuId
            ],
            ['id' => 'desc'],
            1);
        
        if ($telemetry) {
            return $telemetry[0];
        }
        
        return null;
    }
    
    
    /**
     * @param int $rigId
     * @return array
     */
    public function findGpusWithRigid(int $rigId): array
    {
        return $this->selectModel(
            Gpu::class,
            "gpu",
            [],
            [
                'rig_id' => $rigId,
                'active' => 1
            ],
            ['bus' => 'ASC']);
    }
    
    /**
     * @param Rig[] ...$rigs
     * @return bool
     */
    public function updateRig(Rig ...$rigs): bool
    {
        $split = $this->splitIntoInsertUpdate(...$rigs);
        if ($split[self::UPDATES]) {
            $this->updateModel(
                'rig',
                ['name', 'location', 'price'],
                ['id'],
                ...$split[self::UPDATES]
            );
        }
        
        if ($split[self::INSERTS]) {
            $this->insertModel(
                'rig',
                ['user_id', 'reference', 'name', 'location', 'price', 'os_type'],
                ...$split[self::INSERTS]
            );
        }
        
        return true;
    }
    
    /**
     * @param RigTelemetry[] ...$rigTelemetries
     * @return bool
     */
    public function updateRigTelemetry(RigTelemetry ...$rigTelemetries): bool
    {
        $split = $this->splitIntoInsertUpdate(...$rigTelemetries);
        if ($split[self::UPDATES]) {
            $this->updateModel(
                'rig_telemetry',
                ['client_uptime', 'environment_temp', 'cpu_usage', 'cpu_temp', 'ram_usage', 'hash_rate', 'power', 'watt_hours', 'algo'],
                ['id'],
                ...$split[self::UPDATES]
            );
        }
        
        if ($split[self::INSERTS]) {
            $this->insertModel(
                'rig_telemetry',
                ['rig_id', 'client_uptime', 'environment_temp', 'cpu_usage', 'cpu_temp', 'ram_usage', 'hash_rate', 'power', 'watt_hours', 'algo'],
                ...$split[self::INSERTS]
            );
        }
        
        return true;
    }
    
    /**
     * @param Gpu[] ...$gpus
     * @return bool
     */
    public function updateGpu(Gpu ...$gpus): bool
    {
        $split = $this->splitIntoInsertUpdate(...$gpus);
        if ($split[self::UPDATES]) {
            $this->updateModel(
                'gpu',
                ['active'],
                ['id'],
                ...$split[self::UPDATES]
            );
        }
        
        if ($split[self::INSERTS]) {
            $this->insertModel(
                'gpu',
                ['rig_id', 'reference', 'type', 'name', 'bus', 'serial', 'active'],
                ...$split[self::INSERTS]
            );
        }
        
        return true;
    }
    
    /**
     * @param GpuTelemetry[] ...$gpuTelemetries
     * @return bool
     */
    public function updateGpuTelemetry(GpuTelemetry ...$gpuTelemetries): bool
    {
        $split = $this->splitIntoInsertUpdate(...$gpuTelemetries);
        if ($split[self::UPDATES]) {
            $this->updateModel(
                'gpu_telemetry',
                ['core_temp', 'core_usage', 'ram_usage', 'ram_total', 'fan_speed', 'hash_rate', 'power', 'algo'],
                ['id'],
                ...$split[self::UPDATES]
            );
        }
        
        if ($split[self::INSERTS]) {
            $this->insertModel(
                'gpu_telemetry',
                ['gpu_id', 'core_temp', 'core_usage', 'ram_usage', 'ram_total', 'fan_speed', 'hash_rate', 'power', 'algo'],
                ...$split[self::INSERTS]
            );
        }
        
        return true;
    }
    
    /**
     * @param int $days
     */
    public function copyRigTelemetryToHistory(int $days): void
    {
        $this->getConnection()->updateQuery('
        INSERT INTO rig_telemetry_history (`rig_id`, `client_uptime`, `algo`, `min_environment_temp`, `max_environment_temp`, `avg_environment_temp`, `min_cpu_usage`, `max_cpu_usage`, `avg_cpu_usage`, `min_cpu_temp`, `max_cpu_temp`, `avg_cpu_temp`, `min_ram_usage`, `max_ram_usage`, `avg_ram_usage`, `min_hash_rate`, `max_hash_rate`, `avg_hash_rate`, `min_power`, `max_power`, `avg_power`, `watt_hours`, `created`)
        SELECT
        rig_id, MAX(client_uptime) as client_uptime, algo,
        MIN(environment_temp) as min_environment_temp, MAX(environment_temp) as max_environment_temp, ROUND(AVG(environment_temp), 1) as avg_environment_temp,
        MIN(cpu_usage) as min_cpu_usage, MAX(cpu_usage) as max_cpu_usage, ROUND(AVG(cpu_usage), 5) as avg_cpu_usage,
        MIN(cpu_temp) as min_cpu_temp, MAX(cpu_temp) as max_cpu_temp, ROUND(AVG(cpu_temp), 1) as avg_cpu_temp,
        MIN(ram_usage) as min_ram_usage, MAX(ram_usage) as max_ram_usage, ROUND(AVG(ram_usage)) as avg_ram_usage,
        MIN(hash_rate) as min_hash_rate, MAX(hash_rate) as max_hash_rate, ROUND(AVG(hash_rate), 5) as avg_hash_rate,
        MIN(power) as min_power, MAX(power) as max_power, ROUND(AVG(power), 5) as avg_power,
        max(watt_hours) as watt_hours,
        DATE(created) as created
        FROM rig_telemetry
        WHERE DATE(created) < DATE(NOW() - INTERVAL ? DAY)
        GROUP BY YEAR(created), MONTH(created), DAY(created), rig_id, algo
        ORDER BY created ASC
        ', $days);
    }
    
    /**
     * @param int $days
     */
    public function copyRigTelemetryToJunkyard(int $days): void
    {
        $this->getConnection()->updateQuery('
        INSERT INTO `rig_telemetry_junkyard`
        SELECT * FROM rig_telemetry
        WHERE DATE(created) < DATE(NOW() - INTERVAL ? DAY)
        ORDER BY created ASC
        ', $days);
    }
    
    /**
     * @param int $days
     */
    public function deleteOutdatedRigTelemetry(int $days): void
    {
        $this->getConnection()->updateQuery(
            'DELETE FROM rig_telemetry WHERE DATE(created) < DATE(NOW() - INTERVAL ? DAY)',
            $days
        );
    }
    
    /**
     * @param int $days
     */
    public function copyGpuTelemetryToHistory(int $days): void
    {
        $this->getConnection()->updateQuery('
        INSERT INTO gpu_telemetry_history (`gpu_id`, `algo`, `min_core_usage`, `max_core_usage`, `avg_core_usage`, `min_core_temp`, `max_core_temp`, `avg_core_temp`, `min_ram_usage`, `max_ram_usage`, `avg_ram_usage`, `min_ram_total`, `max_ram_total`, `avg_ram_total`, `min_fan_speed`, `max_fan_speed`, `avg_fan_speed`, `min_hash_rate`, `max_hash_rate`, `avg_hash_rate`, `min_power`, `max_power`, `avg_power`, `created`)
        SELECT
        gpu_id, algo,
        MIN(core_usage) as min_core_usage, MAX(core_usage) as max_core_usage, ROUND(AVG(core_usage), 1) as avg_core_usage,
        MIN(core_temp) as min_core_temp, MAX(core_temp) as max_core_temp, ROUND(AVG(core_temp), 1) as avg_core_temp,
        MIN(ram_usage) as min_ram_usage, MAX(ram_usage) as max_ram_usage, ROUND(AVG(ram_usage)) as avg_ram_usage,
        MIN(ram_total) as min_ram_total, MAX(ram_total) as max_ram_total, ROUND(AVG(ram_total)) as avg_ram_total,
        MIN(fan_speed) as min_fan_speed, MAX(fan_speed) as max_fan_speed, ROUND(AVG(fan_speed)) as avg_fan_speed,
        MIN(hash_rate) as min_hash_rate, MAX(hash_rate) as max_hash_rate, ROUND(AVG(hash_rate), 5) as avg_hash_rate,
        MIN(power) as min_power, MAX(power) as max_power, ROUND(AVG(power), 5) as avg_power,
        DATE(created) as created
        FROM gpu_telemetry
        WHERE DATE(created) < DATE(NOW() - INTERVAL ? DAY)
        GROUP BY YEAR(created), MONTH(created), DAY(created), gpu_id, algo
        ORDER BY created ASC
        ', $days);
    }
    
    /**
     * @param int $days
     */
    public function copyGpuTelemetryToJunkyard(int $days): void
    {
        $this->getConnection()->updateQuery('
        INSERT INTO `gpu_telemetry_junkyard`
        SELECT * FROM gpu_telemetry
        WHERE DATE(created) < DATE(NOW() - INTERVAL ? DAY)
        ORDER BY created ASC
        ', $days);
    }
    
    /**
     * @param int $days
     */
    public function deleteOutdatedGpuTelemetry(int $days): void
    {
        $this->getConnection()->updateQuery(
            'DELETE FROM gpu_telemetry WHERE DATE(created) < DATE(NOW() - INTERVAL ? DAY)',
            $days
        );
    }
}