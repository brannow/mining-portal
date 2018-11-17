<?php declare(strict_types=1);


namespace Src\cli\BlockChain\Explorer\Network;

use Src\cli\BlockChain\Explorer\Network\Lib\Insight;

class Zclassic extends Insight
{
    protected const EXPLORER_URL = 'http://explorer.zclmine.pro/insight-api-zcash';
}