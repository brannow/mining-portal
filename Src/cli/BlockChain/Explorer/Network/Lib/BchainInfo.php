<?php declare(strict_types=1);


namespace Src\cli\BlockChain\Explorer\Network\Lib;


abstract class BchainInfo extends RawExplorerParser
{
    protected const SYMBOL = '';
    protected const EXPLORER_URL = 'https://bchain.info';
    
    /**
     * @param string $address
     * @return float
     */
    public function getBalance(string $address): float
    {
        if (static::SYMBOL !== self::SYMBOL) {
            $summery = $this->executePath(static::SYMBOL . '/addr/' . $address);
            if (isset($summery['balance'])) {
                return (float)$summery['balance'];
            }
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
        if (strpos($path, static::SYMBOL . '/addr/') === 0) {
            $removeTopData = substr($data, strpos($data, 'var balance = ') + 14);
            unset($data);
            $balanceString = substr($removeTopData, 0, strpos($removeTopData, ';'));
            unset($removeTopData);
            
            // we only get microCoins
            if (strlen($balanceString) > 8) {
                $balance = (float)substr_replace($balanceString, '.', -8, 0);
            } else {
                $balance = (float)('0.' . str_pad($balanceString, 8, '0', STR_PAD_LEFT));
            }
            
            if ($balance) {
                return [
                    'balance' => $balance
                ];
            }
        }
        
        return [];
    }
}