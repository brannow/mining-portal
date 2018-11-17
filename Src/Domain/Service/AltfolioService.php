<?php declare(strict_types=1);


namespace Src\Domain\Service;


use Src\Domain\Model\Altfolio\Altfolio;
use Src\Domain\Model\Altfolio\Wallet;
use Src\Domain\Model\CurrencyExchangeRate;

class AltfolioService extends BaseService
{
    /**
     * @param Altfolio $altfolio
     * @return float
     */
    public function calculateAltfolio(Altfolio $altfolio): float
    {
        $wallets = $this->getWalletRepository()->findByAltfolioId($altfolio->getId());
        $lastestUSDEurPrice = $this->getCurrencyRepository()->getLatestUsdEur();
        $walletCoins = [];
        /** @var Wallet $wallet */
        foreach ($wallets as $wallet) {
            $walletCoins[$wallet->getCurrencyId()] = $wallet->getCurrencyId();
        }
    
        $exchanges = [];
        if ($walletCoins) {
            $currencies = $this->getCurrencyRepository()->findByIds($walletCoins);
            $exchanges = $this->getCurrencyRepository()->findLatestExchangeRateForCurrency(...$currencies);
        }
        
        $currencyList = [];
        /** @var CurrencyExchangeRate $exchange */
        foreach ($exchanges as $exchange) {
            $currencyList[$exchange->getCurrencyId()]['e'] = $exchange;
        }
    
        $globalEuroPrice = 0;
        /** @var Wallet $wallet */
        foreach ($wallets as $wallet) {
            $eurPrice = 0;
            if ($lastestUSDEurPrice && isset($currencyList[$wallet->getCurrencyId()]['e'])) {
                /** @var CurrencyExchangeRate $exchange */
                $exchange = $currencyList[$wallet->getCurrencyId()]['e'];
                $eurPrice = ($exchange->getUsd() * $wallet->getAmount()) * $lastestUSDEurPrice->getRate();
            }
        
            $globalEuroPrice += $eurPrice;
        }
        
        return $globalEuroPrice;
    }
}