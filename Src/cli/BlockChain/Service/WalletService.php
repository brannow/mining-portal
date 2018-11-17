<?php declare(strict_types=1);


namespace Src\cli\BlockChain\Service;


use Src\cli\BlockChain\Explorer\BlockChain;
use Src\Domain\Model\Altfolio\Wallet;
use Src\Domain\Model\Currency;
use Src\Domain\Repository\CurrencyRepository;

class WalletService
{
    /**
     * @var CurrencyRepository
     */
    private $currencyRepository = null;
    
    /**
     * @var array
     */
    private $currencies = [];
    
    /**
     * @return CurrencyRepository
     */
    private function getCurrencyRepository(): CurrencyRepository
    {
        if (!$this->currencyRepository) {
            $this->currencyRepository = new CurrencyRepository();
        }
        
        return $this->currencyRepository;
    }
    
    /**
     * @param Wallet $wallet
     * @return null|Currency
     */
    private function getCurrencyForWallet(Wallet $wallet): ?Currency
    {
        if (!$this->currencies) {
            $allCurrencies = $this->getCurrencyRepository()->findAllCurrencies();
            $cc = [];
            /** @var Currency $c */
            foreach ($allCurrencies as $c) {
                $cc[$c->getId()] = $c;
            }
            $this->currencies = $cc;
        }
        
        if (isset($this->currencies[$wallet->getCurrencyId()])) {
            return $this->currencies[$wallet->getCurrencyId()];
        }
        
        return null;
    }
    
    /**
     * @param Wallet[] ...$wallets
     */
    public function updateWallets(Wallet ...$wallets)
    {
        /** @var Wallet $wallet */
        foreach ($wallets as $wallet) {
            $currency = $this->getCurrencyForWallet($wallet);
            if ($currency) {
                $amount = BlockChain::getBalance($currency->getSymbol(), $wallet->getAddress());
                $wallet->setAmount($amount);
            }
        }
    }
}