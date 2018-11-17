<?php declare(strict_types=1);


namespace Src\cli\BlockChain\Controller;


use Fuyukai\Kernel;
use Fuyukai\Userspace\Controller\CliController;
use Src\cli\BlockChain\Service\WalletService;
use Src\Domain\Repository\WalletRepository;

class WalletController extends CliController
{
    /**
     * @var WalletRepository
     */
    private $walletRepository = null;
    
    /**
     * @return WalletRepository
     */
    private function getWalletRepository(): WalletRepository
    {
        if (!$this->walletRepository) {
            $this->walletRepository = new WalletRepository();
        }
        
        return $this->walletRepository;
    }
    
    /**
     * @return string
     */
    public function updateWalletAmount()
    {
        Kernel::increaseExecutionTime(320);
        
        $wallets = $this->getWalletRepository()->findAll();
        $walletService = new WalletService();
        $walletService->updateWallets(...$wallets);
        $this->getWalletRepository()->updateWallet(...$wallets);
        
        return 'success';
    }
}