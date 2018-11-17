<?php declare(strict_types=1);


namespace Src\Domain\Enum;


use Fuyukai\Userspace\Enum\AbstractEnum;

abstract class RemoteBlockChainTypes extends AbstractEnum
{
    public const __DEFAULT = 0;
    
    public const BULWARK = 'BWK';
    public const INNOVA = 'INN';
    public const LUXCOIN = 'LUX';
    
    public static $classMapping = [
        //self::VERGE => 'Src\cli\BlockChain\Explorer\Network\Verge',
        self::BULWARK => 'Src\cli\BlockChain\Explorer\Network\Bulwark',
        self::INNOVA => 'Src\cli\BlockChain\Explorer\Network\Innova',
        self::LUXCOIN => 'Src\cli\BlockChain\Explorer\Network\Luxcoin',
        //self::LBRY => 'Src\cli\BlockChain\Explorer\Network\Lbry',
        //self::VERTCOIN => 'Src\cli\BlockChain\Explorer\Network\Vertcoin',
        //self::ZCLASSIC => 'Src\cli\BlockChain\Explorer\Network\Zclassic',
        //self::ZENCASH => 'Src\cli\BlockChain\Explorer\Network\Zencash',
        //self::HUSH => 'Src\cli\BlockChain\Explorer\Network\Hush'
    ];
}