<?php declare(strict_types=1);


namespace Src\Frontend\RenderService;


use Src\Domain\Model\Altfolio\Altfolio;
use Src\Domain\Model\Altfolio\Wallet;
use Src\Domain\Model\Currency;
use Src\Domain\Model\CurrencyExchangeRate;
use Src\Domain\Repository\CurrencyRepository;
use Src\Domain\Repository\WalletRepository;
use Src\Domain\Service\AltfolioService;

abstract class AltfolioRenderer
{
    /**
     * @param Altfolio[] ...$altfolios
     * @return string
     */
    public static function renderAltfolios(Altfolio ...$altfolios): string
    {
        $as = new AltfolioService();
        $table = '<table style="width: 100%;">';
        
        /** @var Altfolio $altfolio */
        foreach ($altfolios as $altfolio) {
            $table .= '<tr>';
            $table .= '<td><a href="/altfolio/index?altfolioId='. $altfolio->getId() .'">' . $altfolio->getName() . '</a></td>';
            $table .= '<td style="text-align: right;">' . round($as->calculateAltfolio($altfolio), 2) . ' €</td>';
            $table .= '<td style="text-align: right; width: 100px;">
                            <a href="/altfolio/edit?id='.$altfolio->getId().'">edit</a>
                            <a href="/altfolio/delete?id='.$altfolio->getId().'">delete</a>
                        </td>';
            $table .= '</tr>';
        }
        
        $table .= '</table>';
        
        return $table;
    }
    
    public static function renderWallets(Altfolio $altfolio): string
    {
        $walletRepo = new WalletRepository();
        $currencyRepo = new CurrencyRepository();
        $lastestUSDEurPrice = $currencyRepo->getLatestUsdEur();
        $wallets = $walletRepo->findByAltfolioId($altfolio->getId());
        $currencyIds = [];
        /** @var Wallet $wallet */
        foreach ($wallets as $wallet) {
            $currencyIds[$wallet->getCurrencyId()] = $wallet->getCurrencyId();
        }
    
        $currencies = [];
        $exchanges = [];
        if ($currencyIds) {
            $currencies = $currencyRepo->findByIds($currencyIds);
            $exchanges = $currencyRepo->findLatestExchangeRateForCurrency(...$currencies);
        }
        
        /** @var Currency $currency */
        $currencyList = [];
        foreach ($currencies as $currency) {
            $currencyList[$currency->getId()]['c'] = $currency;
        }
        /** @var CurrencyExchangeRate $exchange */
        foreach ($exchanges as $exchange) {
            $currencyList[$exchange->getCurrencyId()]['e'] = $exchange;
        }
        
        $table = '<table style="width: 100%;">';
        $table .= '<tr class="nonHover"><th>Address</th><th>Coin</th><th>Amount</th><th>EUR</th><th></th></tr>';
        
        $globalEuroPrice = 0;
        /** @var Wallet $wallet */
        foreach ($wallets as $wallet) {
            
            $coinName = '';
            $eurPrice = 0;
            if (isset($currencyList[$wallet->getCurrencyId()]['c'])) {
                /** @var Currency $coin */
                $coin = $currencyList[$wallet->getCurrencyId()]['c'];
                $coinName = $coin->getSymbol();
                if ($coin->getName()) {
                    $coinName .= ' ('. $coin->getName() .')';
                }
                if ($lastestUSDEurPrice && isset($currencyList[$wallet->getCurrencyId()]['e'])) {
                    /** @var CurrencyExchangeRate $exchange */
                    $exchange = $currencyList[$wallet->getCurrencyId()]['e'];
                    $eurPrice = ($exchange->getUsd() * $wallet->getAmount()) * $lastestUSDEurPrice->getRate();
                }
            }
    
            $globalEuroPrice += $eurPrice;
            
            $table .= '<tr>';
                $table .= '<td>'.$wallet->getAddress().'</td>';
                $table .= '<td>'. $coinName .'</td>';
                $table .= '<td>'. $wallet->getAmount() .'</td>';
                $table .= '<td>'. round($eurPrice, 2) .' €</td>';
                $table .= '<td><a href="/altfolio/wallet/delete?altfolioId='. $altfolio->getId() .'&wallet='. $wallet->getId() .'">X</a></td>';
            $table .= '</tr>';
        }
    
        $table .= '<tr style="border-top: #cccccc 1px solid">';
        $table .= '<td colspan="3"></td>';
        $table .= '<td>'. round($globalEuroPrice, 2) .' €</td>';
        $table .= '</tr>';
        
        $table .= '</table>';
        return $table;
    }
}