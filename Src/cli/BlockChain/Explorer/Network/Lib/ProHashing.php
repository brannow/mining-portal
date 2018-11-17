<?php declare(strict_types=1);


namespace Src\cli\BlockChain\Explorer\Network\Lib;


abstract class ProHashing extends BlockExplorer implements BlockExplorerInterface
{
    protected const EXPLORER_URL = 'https://prohashing.com/explorerJson';
    protected const COIN_ID = 0;
    
    /**
     * @param string $address
     * @return float
     */
    public function getBalance(string $address): float
    {
        $data = $this->apiCall('getAddress', ['address' => $address]);
        if (isset($data['balance'])) {
            return (float)$data['balance'];
        }
        
        return 0.0;
    }
    
    /**
     * @param string $action
     * @param array $data
     * @return array
     */
    private function apiCall(string $action, array $data): array
    {
        // must be overwritten
        if (static::COIN_ID !== self::COIN_ID) {
            $data['coin_id'] = static::COIN_ID;
    
            return static::execute(
                static::EXPLORER_URL . '/' . $action,
                $data
            );
        }
        
        return [];
    }
}