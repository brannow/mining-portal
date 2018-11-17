<?php declare(strict_types=1);


namespace Src\cli\BlockChain\Explorer\Network;

use Src\cli\BlockChain\Explorer\Network\Lib\RawExplorerParser;

class Lbry extends RawExplorerParser
{
    protected const EXPLORER_URL = 'https://explorer.lbry.io';
    
    /**
     * @param string $address
     * @return float
     */
    public function getBalance(string $address): float
    {
        $summery = $this->executePath('address/' . $address);
        
        if (isset($summery['balance'])) {
            return (float)$summery['balance'];
        }
        
        return 0.0;
    }
    
    /**
     * @param string $data
     * @param string $path
     * @return array
     */
    protected function parseData(string $data, string $path): array
    {
        // address data
        if (strpos($path, 'address/') === 0) {
            $removeTopData = substr($data, strpos($data, '<div class="address-summary">'));
            unset($data);
            $balance = substr($removeTopData, strpos($removeTopData, '<div class="value">') + 19);
            unset($removeTopData);
            $balanceValue = (float)substr($balance, 0,strpos($balance, '</div>'));
            unset($balance);
            
            if ($balanceValue) {
                return [
                    'balance' => $balanceValue
                ];
            }
        }
        
        return [];
    }
}