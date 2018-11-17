<?php declare(strict_types=1);


namespace Src\cli\BlockChain\Explorer\Network;


use Src\cli\BlockChain\Explorer\Network\Lib\IquidusExplorer;

class Innova extends IquidusExplorer
{
    protected const EXPLORER_URL = 'http://explorer.innovacoin.info';
}