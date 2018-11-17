<?php declare(strict_types=1);


namespace Src\cli\BlockChain\Explorer;


use Src\cli\BlockChain\Explorer\Network\Lib\BlockExplorerInterface;
use Src\Domain\Enum\RemoteBlockChainTypes;

class BlockChain
{
    private static $networkCache = [];
    
    /**
     * @param string $symbol
     * @param string $address
     * @return float
     */
    public static function getBalance(string $symbol, string $address): float
    {
        $network = static::getNetworkClass(strtoupper($symbol));
        if ($network) {
            return $network->getBalance($address);
        }
        
        return 0.0;
    }
    
    /**
     * @param string $symbol
     * @return null|BlockExplorerInterface
     */
    private static function getNetworkClass(string $symbol): ?BlockExplorerInterface
    {
        if (isset(self::$networkCache[$symbol])) {
            return self::$networkCache[$symbol];
        }
        
        if (RemoteBlockChainTypes::checkValue($symbol) && isset(RemoteBlockChainTypes::$classMapping[$symbol])) {
            $class = RemoteBlockChainTypes::$classMapping[$symbol];
            if (class_exists($class)) {
                $obj = new $class();
                self::$networkCache[$symbol] = $obj;
                return $obj;
            }
        }
        
        return null;
    }
}