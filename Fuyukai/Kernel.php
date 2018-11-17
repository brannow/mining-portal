<?php declare(strict_types=1);

namespace Fuyukai;

use Config\Config;
use Fuyukai\Core\Database\Connection;
use Fuyukai\Core\Request;
use Fuyukai\Core\Response;
use Fuyukai\Userspace\Controller\AbstractController;
use Fuyukai\Userspace\View\View;

class Kernel
{
    public const MODE_DEFAULT = 0;
    public const MODE_CLI = 1;
    
    public const ENV_PROD = 0;
    public const ENV_DEV = 1;
    
    /**
     * @var Request
     */
    private $request = null;
    
    /**
     * @var Response
     */
    private $response = null;
    
    /**
     * @var int
     */
    private $mode = self::MODE_DEFAULT;
    
    /**
     * @var int
     */
    private static $env = self::ENV_PROD;
    
    private $processingTime = 0.0;
    
    /**
     * Kernel constructor.
     * @param int $mode
     */
    public function __construct(int $mode = self::MODE_DEFAULT)
    {
        $this->processingTime = microtime(true);
        
        $this->request = new Request();
        if ($mode === static::MODE_DEFAULT || $mode === static::MODE_CLI) {
            $this->mode = $mode;
        }
        
        if ($this->request->getEnv() === Request::ENV_DEV) {
            static::$env = self::ENV_DEV;
        }
    }
    
    /**
     * @param string $path
     */
    public function setCLIRoutePath(string $path)
    {
        if ($this->mode === self::MODE_CLI) {
            $this->request->setPath($path);
        }
    }
    
    /**
     *
     */
    public function execute(): void
    {
        $result = '';
        $view = null;
        
        if ($this->mode === self::MODE_CLI) {
            $routingConfig = Config::getCLIRoutingEntry($this->request->getPath());
        } else {
            $routingConfig = Config::getRoutingEntry($this->request->getPath());
        }
        
        if ($routingConfig && class_exists($routingConfig[Config::CONTROLLER])) {
            $className = (string)$routingConfig[Config::CONTROLLER];
            $methodName = (string)$routingConfig[Config::METHOD];
            $object = new $className($methodName, $this->request, $this->mode);
            
            if ($object && $object instanceof AbstractController && $methodName) {
                $controllerResult = (string)$object->callAction($methodName);
                
                if ($controllerResult) {
                    $result = $controllerResult;
                } elseif ($this->mode === self::MODE_DEFAULT) {
                    $object->willRenderView();
                    $view = $object->getView();
                    if ($view && $view instanceof View) {
                        $result = $view->render();
                    }
                    $object->didRenderView();
                }
            }
        }
        
        $this->response = new Response($result);
        if ($this->mode === self::MODE_DEFAULT && $view && $view instanceof View) {
            $this->response->setCustomHeader($view->getHeader());
        }
    }
    
    /**
     *
     */
    public function shutdown(): void
    {
        Connection::shutdown();
        
        if ($this->response && $this->mode === self::MODE_DEFAULT) {
            if (static::isDev()) {
                $this->response->setCustomHeader(['Fuyukai-Processing-Time' => (microtime(true) - $this->processingTime)]);
            }
            $this->response->send();
        } elseif ($this->response && $this->mode === self::MODE_CLI) {
            $this->response->sendContent();
        }
    }
    
    /**
     * @return bool
     */
    public static function isDev(): bool
    {
        return (bool)(static::$env === self::ENV_DEV);
    }
    
    /**
     * @param int $seconds
     */
    public static function increaseExecutionTime(int $seconds)
    {
        set_time_limit($seconds);
        ini_set('max_execution_time', (string)$seconds);
    }
    
    /**
     * @param string $path
     */
    public static function redirectResponse(string $path)
    {
        if ($path) {
            $redirectResponse = new Response('', 302);
            $redirectResponse->setCustomHeader(['Location' => $path]);
            $redirectResponse->send();
            die();
        }
    }
}
