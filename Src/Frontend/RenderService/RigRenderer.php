<?php declare(strict_types=1);


namespace Src\Frontend\RenderService;


use Src\Domain\Service\RigDataService;

abstract class RigRenderer
{
    /**
     * @param array $rigData
     * @return string
     */
    public static function renderRigBaseDataTable(array $rigData): string
    {
        return '<table style="width: 100%"><thead>'.
                '<tr class="nonHover">'.
                '<th></th>'.
                '<th>Name</th>'.
                '<th>Uptime</th>'.
                '<th>Cpu</th>'.
                '<th>Ram</th>'.
                '<th>Rate</th>'.
                '<th>Power</th>'.
                '<th>KW/h</th>'.
                '<th>Update</th>'.
                '</tr>'.
                '</thead>'.
                '<tbody>'. static::renderRigBaseDataRows($rigData) .'</tbody></table>';
    }
    
    /**
     * @param $gpuData
     * @return string
     */
    public static function renderRigGPUBaseDataTable($gpuData): string
    {
        return '<table style="width: 97%;margin-left: 3%;margin-top: 10px;"><thead>'.
            '<tr class="nonHover">'.
            '<th></th>'.
            '<th>Name</th>'.
            '<th>Rate</th>'.
            '<th>Core</th>'.
            '<th>Temp</th>'.
            '<th>Fan</th>'.
            '<th>Ram</th>'.
            '<th>Power</th>'.
            '</tr>'.
            '</thead>'.
            '<tbody>'. static::renderGpuBaseDataRows($gpuData) .'</tbody></table>';
    }
    
    /**
     * @param array $boxes
     * @return string
     */
    public static function renderRigAdvancedBoxes(array $boxes): string
    {
        $htmlBoxes = '';
        foreach ($boxes as $box) {
            $htmlBoxes .= static::renderRigAdvancedBox($box);
        }
        
        return $htmlBoxes;
    }
    
    /**
     * @param array $box
     * @return string
     */
    public static function renderRigAdvancedBox(array $box): string
    {
        $rigHeader = '<div class="circle indicator-status-'. $box[RigDataService::RIG_STATUS] .'"></div> '.
            $box[RigDataService::RIG_NAME];
        
        $roomTempHeader = '';
        if (!empty($box[RigDataService::RIG_ENVIRONMENT_TEMP])) {
            $roomTempHeader = '<th>Room</th>';
        }
        
        $boxHTML = '<div class="box"><div class="header">
                            '. $rigHeader .'
                        </div>
                        <table style="width: 100%">' .
                        '<tr class="nonHover">'.
                        '<th></th>'.
                        '<th>Uptime</th>'.
                        $roomTempHeader.
                        '<th>Cpu</th>'.
                        '<th>Ram</th>'.
                        '<th>Rate</th>'.
                        '<th>Power</th>'.
                        '<th>KW/h</th>'.
                        '<th>Update</th>'.
                        '</tr>'.
                        static::renderRigBaseDataRow($box, true).'</table>'.
                        static::renderRigGPUBaseDataTable($box[RigDataService::RIG_GPU])
                        .'</div>';
        
        return $boxHTML;
    }
    
    /**
     * @param array $rigData
     * @return string
     */
    public static function renderRigBaseDataRows(array $rigData): string
    {
        $rows = '';
        foreach ($rigData as $rigItem) {
            $rows .= static::renderRigBaseDataRow($rigItem);
        }
        
        return $rows;
    }
    
    /**
     * @param array $gpuData
     * @return string
     */
    public static function renderGpuBaseDataRows(array $gpuData): string
    {
        $rows = '';
        foreach ($gpuData as $gpuItem) {
            $rows .= static::renderGpuBaseDataRow($gpuItem);
        }
    
        return $rows;
    }
    
    /**
     * @param array $rigData
     * @param bool $withoutName
     * @return string
     */
    public static function renderRigBaseDataRow(array $rigData, bool $withoutName = false): string
    {
        $data = '<tr>';
        if (!$withoutName) {
            $data .= '<td width="15px"><div class="circle indicator-status-'. $rigData[RigDataService::RIG_STATUS] .'"></div></td>';
            $data .= '<td>'.
                '<img width="12px" title="'. $rigData[RigDataService::RIG_OS] .'" src="/Resources/img/icon/rig/'. $rigData[RigDataService::RIG_OS] .'-icon.png" /> ' .
                $rigData[RigDataService::RIG_NAME] . '</td>';
    
        } else {
            $data .= '<td width="15px"><img width="12px" title="'. $rigData[RigDataService::RIG_OS] .'" src="/Resources/img/icon/rig/'. $rigData[RigDataService::RIG_OS] .'-icon.png" /></td>';
        }
        $data .= '<td>' . $rigData[RigDataService::RIG_UPTIME_CLIENT] . '</td>';
        if ($withoutName && !empty($rigData[RigDataService::RIG_ENVIRONMENT_TEMP])) {
            $data .= '<td>' . number_format($rigData[RigDataService::RIG_ENVIRONMENT_TEMP], 1) . ' °C</td>';
        }
        $data .= '<td title="'. $rigData[RigDataService::RIG_CPU_TEMP].' °C">' . $rigData[RigDataService::RIG_CPU_USAGE] . ' %</td>';
        $data .= '<td>' . $rigData[RigDataService::RIG_RAM_USAGE] . ' %</td>';
        $data .= '<td>' . $rigData[RigDataService::RIG_HASH_RATE] . '</td>';
        $data .= '<td>' . $rigData[RigDataService::RIG_POWER] . ' W</td>';
        $data .= '<td>' . number_format((float)$rigData[RigDataService::RIG_WATT_HOURS], 2) . ' KW/h</td>';
        $data .= '<td>' . $rigData[RigDataService::RIG_LAST_UPDATE_INTERVAL] . ' min</td>';
        
        return $data.'</tr>';
    }
    
    /**
     * @param array $gpuBaseData
     * @return string
     */
    public static function renderGpuBaseDataRow(array $gpuBaseData): string
    {
        $data = '<tr>';
        $data .= '<td width="15px"><img width="12px" title="'. $gpuBaseData[RigDataService::RIG_GPU_TYPE] .'" src="/Resources/img/icon/rig/gpu/'. $gpuBaseData[RigDataService::RIG_GPU_TYPE] .'.png" /> </td>';
        $data .= '<td>' . $gpuBaseData[RigDataService::RIG_GPU_NAME] . '</td>';
        $data .= '<td>' . $gpuBaseData[RigDataService::RIG_GPU_HASH_RATE] . '</td>';
        $data .= '<td>' . $gpuBaseData[RigDataService::RIG_GPU_USAGE] . ' %</td>';
        $data .= '<td>' . $gpuBaseData[RigDataService::RIG_GPU_TEMP] . ' °C</td>';
        $data .= '<td>' . $gpuBaseData[RigDataService::RIG_GPU_FAN] . '</td>';
        $data .= '<td>' . $gpuBaseData[RigDataService::RIG_GPU_RAM_USAGE] . ' %</td>';
        $data .= '<td>' . $gpuBaseData[RigDataService::RIG_GPU_POWER] . ' W</td>';
    
        return $data.'</tr>';
    }
}