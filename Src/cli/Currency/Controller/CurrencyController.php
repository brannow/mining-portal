<?php declare(strict_types=1);


namespace Src\cli\Currency\Controller;

use Fuyukai\Userspace\Controller\CliController;
use Src\cli\Currency\Parser\Currency\CoinMarketCap;
use Src\cli\Currency\Parser\Currency\CurrencyLayer;
use Src\Domain\Model\Currency;
use Src\Domain\Model\CurrencyExchangeRate;
use Src\Domain\Repository\CurrencyRepository;

class CurrencyController extends CliController
{
    /**
     * @var CurrencyRepository
     */
    private $currencyRepository = null;
    
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
     * @return string
     */
    public function updateUsdEuro(): string
    {
        $usdEur = CurrencyLayer::latest();
        if ($usdEur) {
            $this->getCurrencyRepository()->updateUsdEur($usdEur);
            return 'update rate: ' . $usdEur->getRate() . PHP_EOL;
        }
        
        return 'no rate found' . PHP_EOL;
    }
    
    /**
     * @return string
     */
    public function updateCryptoCurrencies(): string
    {
        $currencies = $this->getCurrencyRepository()->findAllCurrencies();
        $somethingUpdated = false;
        if ($currencies) {
            $exchangeRates = CoinMarketCap::ticker(...$currencies);
            if ($exchangeRates) {
                $latestExchangeRates = $this->getCurrencyRepository()->findLatestExchangeRateForCurrency(...$currencies);
                $currencyExchangeRateList = [];
                /** @var CurrencyExchangeRate $exchangeRate */
                foreach ($latestExchangeRates as $exchangeRate) {
                    $currencyExchangeRateList[$exchangeRate->getCurrencyId()] = $exchangeRate;
                }
                
                $exchangeRateObjects = [];
                foreach ($exchangeRates as $exchangeRateDataSet) {
                    
                    $currency = null;
                    $exchangeRate = null;
                    if (isset($exchangeRateDataSet['c']) && isset($exchangeRateDataSet['e'])) {
                        /** @var Currency $currency */
                        $currency = $exchangeRateDataSet['c'];
                        /** @var CurrencyExchangeRate $exchangeRate */
                        $exchangeRate = $exchangeRateDataSet['e'];
                        $this->getCurrencyRepository()->updateCurrency($currency);
                        $latestExchangeRate = null;
                        if (isset($currencyExchangeRateList[$currency->getId()])) {
                            $latestExchangeRate = $currencyExchangeRateList[$currency->getId()];
                        }
                        
                        // no changes, we dont need an update
                        if ($latestExchangeRate && $latestExchangeRate->getUpdated() === $exchangeRate->getUpdated()) {
                            continue;
                        }
                        
                        // only create every new day a new database row, same day simply update
                         if ($latestExchangeRate && substr($exchangeRate->getUpdated(), 0, 10) === substr($latestExchangeRate->getUpdated(), 0, 10)) {
                             $latestExchangeRate->setBtc($exchangeRate->getBtc());
                             $latestExchangeRate->setUsd($exchangeRate->getUsd());
                             if ($latestExchangeRate->getBtcLowest() > $exchangeRate->getBtcLowest()) {
                                 $latestExchangeRate->setBtcLowest($exchangeRate->getBtcLowest());
                             }
                             if ($latestExchangeRate->getBtcHighest() < $exchangeRate->getBtcHighest()) {
                                 $latestExchangeRate->setBtcHighest($exchangeRate->getBtcHighest());
                             }
                             if ($latestExchangeRate->getUsdLowest() > $exchangeRate->getUsdLowest()) {
                                 $latestExchangeRate->setUsdLowest($exchangeRate->getUsdLowest());
                             }
                             if ($latestExchangeRate->getUsdHighest() < $exchangeRate->getUsdHighest()) {
                                 $latestExchangeRate->setUsdHighest($exchangeRate->getUsdHighest());
                             }
                             
                             $latestExchangeRate->setUpdated($exchangeRate->getUpdated());
                             $exchangeRate = $latestExchangeRate;
                         }
                        if ($exchangeRate) {
                            $exchangeRateObjects[] = $exchangeRate;
                            $somethingUpdated = true;
                        }
                    }
                }
    
                if ($exchangeRateObjects) {
                    $this->getCurrencyRepository()->updateCurrencyExchangeRate(...$exchangeRateObjects);
                }
            }
        }
        
        if ($somethingUpdated) {
            return 'success' . PHP_EOL;
        }
    
        return 'nothing found' . PHP_EOL;
    }
}