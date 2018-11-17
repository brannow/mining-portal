<?php declare(strict_types=1);


namespace Src\cli\BlockChain\Explorer\Network;

use Src\cli\BlockChain\Explorer\Network\Lib\Insight;

class Hush extends Insight
{
    protected const EXPLORER_URL = 'https://explorer.myhush.org/api';
}