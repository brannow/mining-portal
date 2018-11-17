<?php declare(strict_types=1);


namespace Src\cli\BlockChain\Explorer\Network;

use Src\cli\BlockChain\Explorer\Network\Lib\BchainInfo;
use Src\Domain\Enum\RemoteBlockChainTypes;

class Vertcoin extends BchainInfo
{
    protected const SYMBOL = RemoteBlockChainTypes::VERTCOIN;
}