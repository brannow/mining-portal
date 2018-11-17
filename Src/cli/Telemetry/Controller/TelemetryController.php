<?php declare(strict_types=1);


namespace Src\cli\Telemetry\Controller;


use Fuyukai\Userspace\Controller\CliController;
use Src\Domain\Repository\RigRepository;

class TelemetryController extends CliController
{
    private const MAX_TELEMETRY_AGE_DAY = 3;
    
    /**
     *
     */
    public function shrinkTelemetry()
    {
        $rigRepo = new RigRepository();
        $rigRepo->copyRigTelemetryToHistory(self::MAX_TELEMETRY_AGE_DAY);
        $rigRepo->copyRigTelemetryToJunkyard(self::MAX_TELEMETRY_AGE_DAY);
        $rigRepo->deleteOutdatedRigTelemetry(self::MAX_TELEMETRY_AGE_DAY);
        
        $rigRepo->copyGpuTelemetryToHistory(self::MAX_TELEMETRY_AGE_DAY);
        $rigRepo->copyGpuTelemetryToJunkyard(self::MAX_TELEMETRY_AGE_DAY);
        $rigRepo->deleteOutdatedGpuTelemetry(self::MAX_TELEMETRY_AGE_DAY);
        return 'success';
    }
}