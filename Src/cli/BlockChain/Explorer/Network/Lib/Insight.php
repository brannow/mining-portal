<?php declare(strict_types=1);


namespace Src\cli\BlockChain\Explorer\Network\Lib;


abstract class Insight extends BlockExplorer implements BlockExplorerInterface
{
    protected const EXPLORER_URL = '';
    
    /**
     * @param string $address
     * @return float
     */
    public function getBalance(string $address): float
    {
        $data = $this->apiCall('addr', $address);
        
        if (isset($data['balance'])) {
            return (float)$data['balance'];
        }
        
        return 0.0;
    }
    
    /**
     * @param string $action
     * @param string $data
     * @return array
     */
    private function apiCall(string $action, string $data): array
    {
        if (static::EXPLORER_URL !== self::EXPLORER_URL) {
            return static::execute(
                static::EXPLORER_URL . '/' . $action . '/' . $data,
                [
                    'noTxList' => 1
                ]
            );
        }
        
        return [];
    }
}