<?php declare(strict_types=1);


namespace Src\cli\Currency\Parser\Currency;


use Fuyukai\Userspace\Parser\JsonParser;
use Src\Domain\Model\Currency;
use Src\Domain\Model\CurrencyExchangeRate;
use Src\Domain\Repository\BaseRepository;


class CoinMarketCap extends JsonParser
{
    /**
     * set the maximum age of an updated currency dataSet in seconds
     * one day = 86400
     */
    private const MAX_OLD_UPDATE = 86400;
    
    private const BASE_URL = 'https://api.coinmarketcap.com/v1/';
    
    /**
     * @param Currency[] ...$currencies
     * @return array
     */
    public static function ticker(Currency ...$currencies): array
    {
        $result = static::execute(
            static::BASE_URL . 'ticker/',
            ['limit' => 100000]
        );
    
        $exchangeRates = [];
        $currencySymbolMap = [];
        $oldestPossibleTime = time() - static::MAX_OLD_UPDATE;
        foreach ($currencies as $currency) {
            $currencySymbolMap[$currency->getSymbol()] = $currency;
        }
        
        $modalDateTime = new \DateTime();
        
        foreach ($result as $coinDataSet) {
            if (!empty($coinDataSet['symbol'])) {
                $symbol = $coinDataSet['symbol'];
                if (!empty($currencySymbolMap[$symbol])) {
                    
                    /** @var Currency $currency */
                    $currency = $currencySymbolMap[$symbol];
                    $usd = (float)$coinDataSet['price_usd'];
                    $btc = (float)$coinDataSet['price_btc'];
                    $name = (string)trim($coinDataSet['name']);
                    $timestamp = (int)$coinDataSet['last_updated'];
                    
                    if ($currency && $usd > 0.0 && $btc > 0.0 && $timestamp > $oldestPossibleTime) {
                        $lastUpdated = clone $modalDateTime;
                        $lastUpdated->setTimestamp($timestamp);
                        $exchangeRate = new CurrencyExchangeRate();
                        $exchangeRate->setBtc($btc);
                        $exchangeRate->setBtcHighest($btc);
                        $exchangeRate->setBtcLowest($btc);
                        $exchangeRate->setUsd($usd);
                        $exchangeRate->setUsdHighest($usd);
                        $exchangeRate->setUsdLowest($usd);
                        $exchangeRate->setUpdated($lastUpdated->format(BaseRepository::MYSQL_DATETIME_FORMAT), true);
                        $exchangeRate->setCurrencyId($currency->getId());
                        $exchangeRates[] = [
                            'e' => $exchangeRate,
                            'c' => $currency
                        ];
    
                        $cname = $currency->getName();
                        if (!$cname || $cname === $symbol) {
                            $currency->setName($name);
                        }
                    }
                }
            }
        }
        
        return $exchangeRates;
    }
}