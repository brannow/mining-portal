<?php declare(strict_types=1);


namespace Fuyukai\Userspace\Controller;


use Config\Config;
use Fuyukai\Userspace\Service\LoginService;
use Src\Domain\Enum\StatusCodes;
use Src\Domain\Model\User;

abstract class LoginController extends AbstractController
{
    /**
     * @var User
     */
    private $user = null;
    
    /**
     * @var LoginService
     */
    private $loginService;
    
    /**
     * @return null|User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }
    
    /**
     * @param null|User $user
     */
    protected function logoutUser(?User $user = null)
    {
        $this->getLoginService()->destroySession();
        $this->redirect('/');
    }
    
    /**
     * @param string $templatePath
     */
    protected function initialize(string $templatePath = '')
    {
        if (!$this->getRequest()) {
            die('0x000007');
        }
        
        $this->getLoginService()->init();
        
        if ($this->tryLoginUser()) {
            // user is logged in - continue with actual workflow
            parent::initialize($templatePath);
        } else {
            // interrupt workflow, show login mask
            $this->initializeView((string)Config::getConfigEntry(Config::LOGIN_TEMPLATE));
        }
    }
    
    /**
     * @param string $methodName
     * @return string
     */
    public function callAction(string $methodName): string
    {
        // user found call actual method
        if ($this->getUser() !== null) {
            $this->assign('username', htmlentities($this->getUser()->getUsername()));
            return parent::callAction($methodName);
        }
    
        // call login method instead
        return (string)$this->loginAction();
    }
    
    /**
     *
     */
    private function loginAction()
    {
        $this->assign('title', 'Login');
        $this->getView()->injectCss('fuyukai.css');
        $this->getView()->injectJs('fuyukai.js');
        $this->assign('flashMessage', StatusCodes::statusCodeToMessage($this->getLoginService()->getErrorCode()));
        $this->assign('__RequestVerificationToken', $this->getLoginService()->generateCSRFToken());
    }
    
    /**
     *
     */
    private function tryLoginUser(): bool
    {
        // is already logged in look for a session and try to fetch user object
        $this->getLoginService()->setSpoofProtectionRequestKey($this->getRequest()->getClientIPHash());
        $this->user = $this->getLoginService()->recoverUserFromSession();
        if ($this->getUser()) {
            return true;
        }
        
        // not already logged in but maybe he tried to login?
        if ($this->getRequest()->isPostRequest()) {
            $csrf = $this->getRequest()->getPostData('__RequestVerificationToken');
            if ($this->getLoginService()->validateCSRFToken($csrf)) {
                
                $username = $this->getRequest()->getPostData('username');
                $password = $this->getRequest()->getPostData('password');
                if ($this->getLoginService()->tryCreateUserSession($username, $password)) {
                    $this->redirect('/');
                }
            }
        }
        
        return false;
    }
    
    /**
     * @return LoginService
     */
    private function getLoginService(): LoginService
    {
        if (!$this->loginService) {
            $this->loginService = new LoginService();
        }
    
        return $this->loginService;
    }
}