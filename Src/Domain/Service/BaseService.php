<?php declare(strict_types=1);


namespace Src\Domain\Service;


use Src\Domain\Repository\CurrencyRepository;
use Src\Domain\Repository\RigRepository;
use Src\Domain\Repository\WalletRepository;

abstract class BaseService
{
    /**
     * @var RigRepository
     */
    private $rigRepository = null;
    
    /**
     * @var WalletRepository
     */
    private $walletRepository = null;
    
    /**
     * @var CurrencyRepository
     */
    private $currencyRepository = null;

    /**
     * @return RigRepository
     */
    protected function getRigRepository(): RigRepository
    {
        if (!$this->rigRepository) {
            $this->rigRepository = new RigRepository();
        }
        
        return $this->rigRepository;
    }
    
    /**
     * @return CurrencyRepository
     */
    protected function getCurrencyRepository(): CurrencyRepository
    {
        if (!$this->currencyRepository) {
            $this->currencyRepository = new CurrencyRepository();
        }
        
        return $this->currencyRepository;
    }
    
    /**
     * @return WalletRepository
     */
    protected function getWalletRepository(): WalletRepository
    {
        if (!$this->walletRepository) {
            $this->walletRepository = new WalletRepository();
        }
        
        return $this->walletRepository;
    }
}