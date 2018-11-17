<?php declare(strict_types=1);


namespace Src\Frontend\User\Controller;


use Fuyukai\Userspace\Controller\LoginController;
use Fuyukai\Userspace\Secure\Encryption;
use Fuyukai\Userspace\Secure\PasswordHashing;
use Src\Domain\Enum\UserLevel;
use Src\Domain\Model\User;
use Src\Domain\Repository\UserRepository;
use Src\Frontend\RenderService\UserRenderer;

class UserController extends LoginController
{
    /**
     *
     */
    public function logout()
    {
        $this->logoutUser();
        $this->redirect('/');
    }
    
    /**
     *
     */
    public function index()
    {
        $this->getView()->injectCss('fuyukai.css');
        $this->getView()->injectJs('fuyukai.js');
        
        $id = (int)$this->getRequest()->getQueryData('id');
        if ($this->getUser()->getLevel() ===UserLevel::ADMIN && $id) {
            $userRepo = new UserRepository();
            $user = $userRepo->findUserById($id);
            if ($user) {
                $this->assign('currentUsername', htmlentities($user->getUsername()));
                $this->assign('title', 'User: ' . htmlentities($user->getUsername()));
                $this->assign('email', htmlentities($user->getEmail()));
                $this->assign('levelName', $user->levelName());
                $this->assign('rigKey', $user->getRigKey());
                $this->assign('editUserId', '?id='.$user->getId());
                
                return;
            }
        }
        
        $this->assign('currentUsername', htmlentities($this->getUser()->getUsername()));
        $this->assign('title', 'User: ' . htmlentities($this->getUser()->getUsername()));
        $this->assign('email', htmlentities($this->getUser()->getEmail()));
        $this->assign('levelName', $this->getUser()->levelName());
        $this->assign('rigKey', $this->getUser()->getRigKey());
        
        if ($this->getUser()->getLevel() === UserLevel::ADMIN) {
            $this->assign('adminPanel', UserRenderer::adminPanel($this->getUser()));
        }
        
    }
    
    /**
     *
     */
    public function create()
    {
        if ($this->getUser()->getLevel() === UserLevel::ADMIN && $this->getRequest()->isPostRequest()) {
            $userRepo = new UserRepository();
            $username = $this->getRequest()->getPostData('username');
            $passwordRaw = $this->getRequest()->getPostData('password');
            $email = $this->getRequest()->getPostData('email');
            $level = (int)$this->getRequest()->getPostData('level');
            $sig = $this->getRequest()->getPostData('signature');
            $genSig = md5($this->getUser()->getId().$this->getUser()->getUsername());
        
            if ($username && $passwordRaw && $email && $level && !$userRepo->existUsername($username) && $genSig === $sig) {
                if (!UserLevel::checkValue($level)) {
                    $level = UserLevel::LOCKED;
                }
                
                $newUser = new User();
                $newUser->setUsername($username);
                $newUser->setEmail($email);
                $newUser->setPassword(PasswordHashing::hashPassword($passwordRaw));
                $newUser->setEncryptionKey(Encryption::generateEncryptionKey($passwordRaw));
                $newUser->setRigKey(md5($username . time()));
                $newUser->setLevel($level);
                $userRepo->updateUser($newUser);
            }
        }
    
        $this->redirect('/user');
    }
    
    /**
     *
     */
    public function delete()
    {
        $userId = (int)$this->getRequest()->getQueryData('id');
        if ($this->getUser()->getLevel() !== UserLevel::ADMIN || !$userId) {
            $this->redirect('/user');
        }
        
        $userRepo = new UserRepository();
        $user = $userRepo->findUserById($userId);
        if (!$user || $this->getUser()->getId() === $user->getId()) {
            $this->redirect('/user');
        }
        
        if ($this->getRequest()->getQueryData('force') === '1') {
            $userRepo->deleteUser($user);
            $this->redirect('/user');
        }
        
        $this->getView()->injectCss('fuyukai.css');
        $this->getView()->injectJs('fuyukai.js');
        $this->assign('title', 'Confirm Delete User: '. htmlentities($user->getUsername()));
        $this->assign('deleteUserName',  htmlentities($user->getUsername()));
        $this->assign('userId',  $user->getId());
    }
    
    /**
     *
     */
    public function edit()
    {
        $user = $this->getUser();
    
        $userId = (int)$this->getRequest()->getQueryData('id');
        if ($this->getUser()->getLevel() === UserLevel::ADMIN && $userId) {
            $userRepo = new UserRepository();
            $fUser = $userRepo->findUserById($userId);
            if ($fUser) {
                $user = $fUser;
            }
        }
    
        $this->getView()->injectCss('fuyukai.css');
        $this->getView()->injectJs('fuyukai.js');
        $this->assign('title', 'Edit: '. htmlentities($user->getUsername()));
        $this->assign('editForm', UserRenderer::editUserForm($this->getUser(), $user));
        
    
    }
    
    public function editProcess()
    {
        if ($this->getRequest()->isPostRequest()) {
            $passwordRaw = $this->getRequest()->getPostData('password');
            $email = $this->getRequest()->getPostData('email');
            $userId = (int)$this->getRequest()->getPostData('userId');
            $level = (int)$this->getRequest()->getPostData('level');
            $sig = $this->getRequest()->getPostData('signature');
    
            $userRepo = new UserRepository();
            $user = $userRepo->findUserById($userId);
            if ($user && $sig === md5($user->getId() . '_' . $this->getUser()->getId().$this->getUser()->getUsername())) {
                if (
                    ($user->getId() !== $this->getUser()->getId() && $this->getUser()->getLevel() === UserLevel::ADMIN) ||
                    $user->getId() === $this->getUser()->getId()
                ) {
                    if ($user->getId() !== $this->getUser()->getId()) {
                        $user->setLevel($level);
                    }
                    
                    if (!empty($passwordRaw)) {
                        $user->setPassword(PasswordHashing::hashPassword($passwordRaw));
                    }
                    
                    $user->setEmail($email);
                    $userRepo->updateUser($user);
                }
            }
        }
        
        $this->redirect('/user');
    }
}