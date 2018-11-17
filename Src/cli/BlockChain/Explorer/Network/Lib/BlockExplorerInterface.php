<?php declare(strict_types=1);


namespace Src\cli\BlockChain\Explorer\Network\Lib;


interface BlockExplorerInterface
{
    /**
     * @param string $address
     * @return float
     */
    public function getBalance(string $address): float;
}