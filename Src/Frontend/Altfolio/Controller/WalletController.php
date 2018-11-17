<?php declare(strict_types=1);


namespace Src\Frontend\Altfolio\Controller;


use Fuyukai\Userspace\Controller\LoginController;
use Src\Domain\Model\Altfolio\Altfolio;
use Src\Domain\Model\Altfolio\Wallet;
use Src\Domain\Model\Currency;
use Src\Domain\Repository\AltfolioRepository;
use Src\Domain\Repository\CurrencyRepository;
use Src\Domain\Repository\WalletRepository;
use Src\Frontend\RenderService\AltfolioRenderer;

class WalletController extends LoginController
{
    /**
     * @var int
     */
    protected $menuIndex = 3;
    
    /**
     * @var Altfolio
     */
    private $altfolio = null;
    
    /**
     * @param string $templatePath
     */
    protected function initialize(string $templatePath = '')
    {
        parent::initialize($templatePath);
        
        if ($this->getUser()) {
            $id = (int)$this->getRequest()->getQueryData('altfolioId');
            $repo = new AltfolioRepository();
            $this->altfolio = $repo->findById($id, $this->getUser()->getId());
    
            if (!$this->altfolio) {
                $this->redirect('/altfolio');
            }
        }
    }
    
    /**
     *
     */
    public function index()
    {
        $this->getView()->injectCss('fuyukai.css');
        $this->getView()->injectJs('fuyukai.js');
        $this->assign('title', 'Altfolio ' . $this->altfolio->getName());
        $this->assign('altfolio', $this->altfolio->getName());
        $this->assign('altfolioId', $this->altfolio->getId());
        $this->assign('wallets', AltfolioRenderer::renderWallets($this->altfolio));
    }
    
    /**
     *
     */
    public function delete()
    {
        $id = (int)$this->getRequest()->getQueryData('wallet');
        $repo = new WalletRepository();
        $wallet = $repo->findById($id, $this->altfolio->getId());
    
        if (!$wallet) {
            $this->redirect('/altfolio/index?altfolioId='. $this->altfolio->getId());
        }
    
        if ($this->getRequest()->getQueryData('force') === '1') {
            $repo->deleteWallet($wallet);
            $this->redirect('/altfolio/index?altfolioId='. $this->altfolio->getId());
        }
    
        $this->getView()->injectCss('fuyukai.css');
        $this->getView()->injectJs('fuyukai.js');
        $this->assign('title', 'Wallet Delete');
        $this->assign('walletAddress', $wallet->getAddress());
        $this->assign('walletId', $wallet->getId());
        $this->assign('altfolioId', $this->altfolio->getId());
    }
    
    /**
     *
     */
    public function create()
    {
        $signature = md5($this->getUser()->getId() . '_' . $this->altfolio->getId());
        $currencyRepo = new CurrencyRepository();
        
        if ($this->getRequest()->isPostRequest()) {
            $currency = null;
            $address = $this->getRequest()->getPostData('address');
            $symbolId = (int)$this->getRequest()->getPostData('symbol');
            $newSymbol = $this->getRequest()->getPostData('newSymbol');
            if ($symbolId) {
                $currency = $currencyRepo->findById($symbolId);
            }
            
            if ($newSymbol && !$currency) {
                $currency = new Currency();
                $currency->setSymbol(strtoupper($newSymbol));
                $currencyRepo->updateCurrency($currency);
            }
            
            if ($address && $currency && $currency->getId() > 0) {
                $wallet = new Wallet();
                $wallet->setCurrencyId($currency->getId());
                $wallet->setAltfolioId($this->altfolio->getId());
                $wallet->setAddress($address);
                $walletRepo = new WalletRepository();
                $walletRepo->updateWallet($wallet);
                $this->redirect('/altfolio/index?altfolioId='.$this->altfolio->getId());
            }
        }
        
        $symbols = $currencyRepo->findAllCurrencies();
        /** @var Currency $symbol */
        $options = '<option selected="selected" value="">---</option>';
        foreach ($symbols as $symbol) {
            $name = '';
            if ($symbol->getName()) {
                $name = ' ('.$symbol->getName().')';
            }
            $options .= '<option value="'.$symbol->getId().'">'. $symbol->getSymbol() . $name . '</option>';
        }
        
        $this->assign('altfolioId', $this->altfolio->getId());
        $this->assign('signature', $signature);
        $this->assign('symbols', $options);
        $this->assign('title', 'Create Wallet for ' . $this->altfolio->getName());
        $this->getView()->injectCss('fuyukai.css');
        $this->getView()->injectJs('fuyukai.js');
    }
}