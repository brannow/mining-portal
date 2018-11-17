<?php declare(strict_types=1);


namespace Src\Frontend\Rig\Controller;


use Fuyukai\Userspace\Controller\LoginController;
use Src\Domain\Service\RigDataService;
use Src\Frontend\RenderService\RigRenderer;
use Src\Frontend\Rig\Service\RigOverviewService;

class RigController extends LoginController
{
    /**
     * @var int
     */
    protected $menuIndex = 2;
    
    /**
     *
     */
    public function index()
    {
        $rigService = new RigDataService($this->getUser());
        $rigAdvancedData = $rigService->getRigAdvancedTelemetry();
        
        $this->getView()->injectCss('fuyukai.css');
        $this->getView()->injectJs('fuyukai.js');
        $this->assign('title', 'Rig Overview');
        $this->assign('rigBoxes', RigRenderer::renderRigAdvancedBoxes($rigAdvancedData));
    }
}