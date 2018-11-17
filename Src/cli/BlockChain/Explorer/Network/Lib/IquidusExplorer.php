<?php declare(strict_types=1);


namespace Src\cli\BlockChain\Explorer\Network\Lib;


abstract class IquidusExplorer extends BlockExplorer implements BlockExplorerInterface
{
    protected const EXPLORER_URL = '';
    
    /**
     * @param string $address
     * @return float
     */
    public function getBalance(string $address): float
    {
        $data = $this->callAddressApi($address);
        
        if ($data && isset($data['balance'])) {
            return (float)$data['balance'];
        }
        
        return 0.0;
    }
    
    /**
     * @param string $address
     * @return array
     */
    private function callAddressApi(string $address): array
    {
        if (self::EXPLORER_URL === static::EXPLORER_URL) {
            return [];
        }
        
        $data = static::execute(static::EXPLORER_URL . '/ext/getaddress/' . $address);
        if ($data && isset($data['balance'])) {
            return $data;
        }
        
        return [];
    }
}