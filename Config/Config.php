<?php declare(strict_types=1);

namespace Config;

abstract class Config
{
    public const API_CL_KEY = 'currencyLayerApiKey';
    public const SYSTEM_KEY = 'systemKey';
    
    public const FUYUKAI_API_PRIVATE_KEY = 'api-private-key';
    public const FUYUKAI_REQUEST_HEADER = 'fuyukai-request-header';
    
    
    public const DB_HOST = 'host';
    public const DB_USER = 'user';
    public const DB_PASS = 'pass';
    public const DB_DATABASE = 'db';
    public const DB_PORT = 'port';
    
    public const CONTROLLER = 'c';
    public const METHOD = 'm';
    
    public const ROOT_TEMPLATE = 'rootTemplate';
    public const LOGIN_TEMPLATE = 'loginTemplate';
    public const CSS_ROOT_DIR = 'cssRoot';
    public const JS_ROOT_DIR = 'jsRoot';
    
    /**
     * @var array
     */
    private static $config = [
    
        // database config
        self::DB_HOST => '127.0.0.1',
        self::DB_USER => 'db_user',
        self::DB_PASS => 'db_pass',
        self::DB_DATABASE => 'db_name',
        self::DB_PORT => 3306,
        
        // template config
        self::ROOT_TEMPLATE => '/Resources/Template/base.html',
        self::LOGIN_TEMPLATE => '/Resources/Template/login.html',
    
        self::CSS_ROOT_DIR => '/Resources/css',
        self::JS_ROOT_DIR => '/Resources/js',
        
        // API KEYS
        // api CurrencyLayer
        self::API_CL_KEY => 'SECRET',
        
        self::FUYUKAI_REQUEST_HEADER => 'HEADER_API_SECRET',
        self::FUYUKAI_API_PRIVATE_KEY => 'API_DECRYPTION_KEY',
        
        // system decryption key
        self::SYSTEM_KEY => 'SUPER_SECRET'
    ];
    
    private static $routing = [
        // routing
        // Main Site
        '/' => [
            self::CONTROLLER => 'Src\Frontend\Dashboard\Controller\DashboardController',
            self::METHOD => 'index'
        ],
    
        // Rig Area
        '/rig' => [
            self::CONTROLLER => 'Src\Frontend\Rig\Controller\RigController',
            self::METHOD => 'index'
        ],
        
        // altfolio
        '/altfolio' => [
            self::CONTROLLER => 'Src\Frontend\Altfolio\Controller\AltfolioController',
            self::METHOD => 'index'
        ],
        '/altfolio/create' => [
            self::CONTROLLER => 'Src\Frontend\Altfolio\Controller\AltfolioController',
            self::METHOD => 'create'
        ],
        '/altfolio/delete' => [
            self::CONTROLLER => 'Src\Frontend\Altfolio\Controller\AltfolioController',
            self::METHOD => 'delete'
        ],
        '/altfolio/edit' => [
            self::CONTROLLER => 'Src\Frontend\Altfolio\Controller\AltfolioController',
            self::METHOD => 'edit'
        ],
        
        //wallet
        '/altfolio/index' => [
            self::CONTROLLER => 'Src\Frontend\Altfolio\Controller\WalletController',
            self::METHOD => 'index'
        ],
        '/altfolio/wallet/create' => [
            self::CONTROLLER => 'Src\Frontend\Altfolio\Controller\WalletController',
            self::METHOD => 'create'
        ],
        '/altfolio/wallet/delete' => [
            self::CONTROLLER => 'Src\Frontend\Altfolio\Controller\WalletController',
            self::METHOD => 'delete'
        ],
        
        
        // User Area
        '/user' => [
            self::CONTROLLER => 'Src\Frontend\User\Controller\UserController',
            self::METHOD => 'index'
        ],
        '/user/create' => [
            self::CONTROLLER => 'Src\Frontend\User\Controller\UserController',
            self::METHOD => 'create'
        ],
        '/user/delete' => [
            self::CONTROLLER => 'Src\Frontend\User\Controller\UserController',
            self::METHOD => 'delete'
        ],
        '/user/edit' => [
            self::CONTROLLER => 'Src\Frontend\User\Controller\UserController',
            self::METHOD => 'edit'
        ],
        '/user/editProcess' => [
            self::CONTROLLER => 'Src\Frontend\User\Controller\UserController',
            self::METHOD => 'editProcess'
        ],
        
        '/logout' => [
            self::CONTROLLER => 'Src\Frontend\User\Controller\UserController',
            self::METHOD => 'logout'
        ],
        
        // api for rigs
        '/api/telemetry' => [
            self::CONTROLLER => 'Src\Frontend\Api\Controller\ApiController',
            self::METHOD => 'telemetry'
        ]
    ];
    
    /**
     * @var array
     */
    private static $cliRouting = [
        // currency
        'usd-eur-update' => [
            self::CONTROLLER => 'Src\cli\Currency\Controller\CurrencyController',
            self::METHOD => 'updateUsdEuro'
        ],
        'update-crypto-currencies' => [
            self::CONTROLLER => 'Src\cli\Currency\Controller\CurrencyController',
            self::METHOD => 'updateCryptoCurrencies'
        ],
        'shrink-telemetry' => [
            self::CONTROLLER => 'Src\cli\Telemetry\Controller\TelemetryController',
            self::METHOD => 'shrinkTelemetry'
        ],
        'update-wallet-amount' => [
            self::CONTROLLER => 'Src\cli\BlockChain\Controller\WalletController',
            self::METHOD => 'updateWalletAmount'
        ]
    ];
    
    /**
     * @param string $nodeName
     * @return mixed
     */
    public static function getConfigEntry(string $nodeName)
    {
        if(isset(static::$config[$nodeName])) {
            return static::$config[$nodeName];
        }
        
        return '';
    }
    
    /**
     * @param string $path
     * @return array
     */
    public static function getRoutingEntry(string $path): array
    {
        $config = [];
        if (isset(static::$routing[$path])) {
            $config = static::$routing[$path];
        }
        
        return $config;
    }
    
    /**
     * @param string $path
     * @return array
     */
    public static function getCLIRoutingEntry(string $path): array
    {
        $config = [];
        if (isset(static::$cliRouting[$path])) {
            $config = static::$cliRouting[$path];
        }
    
        return $config;
    }
}
