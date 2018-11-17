<?php declare(strict_types=1);


namespace Src\cli\BlockChain\Explorer\Network\Lib;


use Fuyukai\Userspace\Parser\JsonParser;

abstract class BlockExplorer extends JsonParser
{
    protected const EXPLORER_URL = '';
}