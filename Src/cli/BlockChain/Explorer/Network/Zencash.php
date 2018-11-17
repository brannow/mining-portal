<?php declare(strict_types=1);


namespace Src\cli\BlockChain\Explorer\Network;

use Src\cli\BlockChain\Explorer\Network\Lib\Insight;

class Zencash extends Insight
{
    protected const EXPLORER_URL = 'http://explorer.zenmine.pro/insight-api-zen';
}