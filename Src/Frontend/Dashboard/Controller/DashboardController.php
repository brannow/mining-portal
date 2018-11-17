<?php declare(strict_types=1);


namespace Src\Frontend\Dashboard\Controller;

use Fuyukai\Userspace\Controller\LoginController;
use Src\Domain\Service\RigDataService;
use Src\Frontend\RenderService\RigRenderer;

class DashboardController extends LoginController
{
    /**
     * @var int
     */
    protected $menuIndex = 1;
    
    /**
     *
     */
    public function index()
    {
        $rigService = new RigDataService($this->getUser());
        $baseData = $rigService->getRigBaseTelemetry();
        
        $this->getView()->injectCss('fuyukai.css');
        $this->getView()->injectJs('fuyukai.js');
        $this->assign('title', 'Dashboard');
        $this->assign('rickOverViewContent', RigRenderer::renderRigBaseDataTable($baseData));
    }
}