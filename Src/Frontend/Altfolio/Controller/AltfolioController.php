<?php declare(strict_types=1);


namespace Src\Frontend\Altfolio\Controller;


use Fuyukai\Userspace\Controller\LoginController;
use Src\Domain\Model\Altfolio\Altfolio;
use Src\Domain\Repository\AltfolioRepository;
use Src\Frontend\RenderService\AltfolioRenderer;

class AltfolioController extends LoginController
{
    /**
     * @var int
     */
    protected $menuIndex = 3;
    
    /**
     *
     */
    public function index()
    {
        $this->getView()->injectCss('fuyukai.css');
        $this->getView()->injectJs('fuyukai.js');
        $this->assign('title', 'Altfolio Overview');
    
        $repo = new AltfolioRepository();
        $altfolios = $repo->findAll();
        $this->assign('altfolios', AltfolioRenderer::renderAltfolios(...$altfolios));
    }
    
    /**
     *
     */
    public function create()
    {
        $signature = md5($this->getUser()->getId().$this->getUser()->getUsername());
        if ($this->getRequest()->isPostRequest() && $this->getRequest()->getPostData('signature') === $signature) {
            $name = htmlentities(trim($this->getRequest()->getPostData('name')));
            $repo = new AltfolioRepository();
            if ($name && !$repo->existName($name)) {
                $altfolio = new Altfolio();
                $altfolio->setUserId($this->getUser()->getId());
                $altfolio->setName($name);
                $repo->updateAltfolio($altfolio);
            }
            
            $this->redirect('/altfolio');
        }
        
        $this->getView()->injectCss('fuyukai.css');
        $this->getView()->injectJs('fuyukai.js');
        $this->assign('title', 'Altfolio Create');
        $this->assign('signature', $signature);
    }
    
    public function edit()
    {
        $id = (int)$this->getRequest()->getQueryData('id');
        $repo = new AltfolioRepository();
        $altfolio = $repo->findById($id, $this->getUser()->getId());
        
        if (!$altfolio) {
            $this->redirect('/altfolio');
        }
        
        $sig = md5($this->getUser()->getId().'_'.$altfolio->getId().'_'.$this->getUser()->getUsername());
        
        if ($this->getRequest()->isPostRequest() && $sig === $this->getRequest()->getPostData('signature')) {
            $newName = htmlentities(trim($this->getRequest()->getPostData('name')));
            if (!empty($newName)) {
                $altfolio->setName($newName);
                $repo->updateAltfolio($altfolio);
                $this->redirect('/altfolio');
            }
        }
        
        $this->getView()->injectCss('fuyukai.css');
        $this->getView()->injectJs('fuyukai.js');
        $this->assign('signature', $sig);
        $this->assign('title', 'Altfolio Edit '. $altfolio->getName());
        $this->assign('altfolioName', $altfolio->getName());
        $this->assign('altfolioId', $altfolio->getId());
    }
    
    /**
     *
     */
    public function delete()
    {
        $id = (int)$this->getRequest()->getQueryData('id');
        $repo = new AltfolioRepository();
        $altfolio = $repo->findById($id, $this->getUser()->getId());
        
        if (!$altfolio) {
            $this->redirect('/altfolio');
        }
        
        if ($this->getRequest()->getQueryData('force') === '1') {
            $repo->deleteAltfolio($altfolio);
            $this->redirect('/altfolio');
        }
    
        $this->getView()->injectCss('fuyukai.css');
        $this->getView()->injectJs('fuyukai.js');
        $this->assign('title', 'Altfolio Delete');
        $this->assign('altfolioName', $altfolio->getName());
        $this->assign('altfolioId', $altfolio->getId());
    }
}