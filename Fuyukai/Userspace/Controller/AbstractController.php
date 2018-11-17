<?php declare(strict_types=1);


namespace Fuyukai\Userspace\Controller;


use Fuyukai\Core\Request;
use Fuyukai\Kernel;
use Fuyukai\Userspace\View\View;

abstract class AbstractController
{
    /**
     * @var int
     */
    protected $menuIndex = 0;
    
    /**
     * @var View
     */
    private $view = null;
    
    /**
     * @var string
     */
    protected $viewClass = '';
    
    /**
     * @var null|Request
     */
    private $request = null;
    
    /**
     * AbstractController constructor.
     * @param string $methodName
     * @param Request|null $request
     * @param int $kernelMode
     */
    public function __construct(string $methodName, Request $request = null, int $kernelMode = Kernel::MODE_DEFAULT)
    {
        $this->request = $request;
        $templatePath = '';
        if ($kernelMode === Kernel::MODE_DEFAULT) {
            $templatePath = static::class . '\\' . $methodName;
        }
        
        $this->initialize($templatePath);
    }
    
    /**
     * @param string $path
     */
    protected function redirect(string $path)
    {
        Kernel::redirectResponse($path);
    }
    
    /**
     * @param string $templatePath
     */
    protected function initialize(string $templatePath = '')
    {
        if ($templatePath) {
            $segments = explode('\\', $templatePath);
            $modifiedSegments = [];
            foreach ($segments as $segment) {
                if ($segment === 'Controller') {
                    $modifiedSegments[] = 'Template';
                    continue;
                }
                $modifiedSegments[] = $segment;
            }
    
            $templatePath = DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $modifiedSegments) . '.html';
        }
        
        $this->initializeView($templatePath);
    }
    
    /**
     * @param string $templatePath
     */
    protected function initializeView(string $templatePath = '')
    {
        if ($this->viewClass !== '' && class_exists($this->viewClass)) {
            $vc = $this->viewClass;
        } else {
            $vc = View::class;
        }
        
        $customView = new $vc($templatePath);
        if ($customView && $customView instanceof View) {
            $this->view = $customView;
        }
    }
    
    /**
     * @param string $methodName
     * @return string
     */
    public function callAction(string $methodName): string
    {
        if (method_exists($this, $methodName)) {
            return (string)$this->$methodName();
        }
        
        return '';
    }
    
    /**
     * @return View|null
     */
    public function getView(): ?View
    {
        return $this->view;
    }
    
    /**
     * @param string $key
     * @param mixed $value
     */
    public function assign(string $key, $value)
    {
        if ($key && $this->getView()) {
            $this->getView()->assign($key, $value);
        }
    }
    
    /**
     * @param array $keyValueArray
     */
    public function assignMultiple(array $keyValueArray)
    {
        if ($keyValueArray && $this->getView()) {
            $this->getView()->assignMultiple($keyValueArray);
        }
    }
    
    /**
     * @return Request|null
     */
    public function getRequest(): ?Request
    {
        return $this->request;
    }
    
    /**
     *
     */
    public function willRenderView()
    {
        if ($this->getView()->supportHTML()) {
            $this->assign('mainMenuActive', $this->menuIndex);
        }
    }
    
    /**
     *
     */
    public function didRenderView()
    {
    
    }
}