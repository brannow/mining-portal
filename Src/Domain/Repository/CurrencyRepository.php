<?php declare(strict_types=1);


namespace Src\Domain\Repository;


use Src\Domain\Model\Currency;
use Src\Domain\Model\CurrencyExchangeRate;
use Src\Domain\Model\UsdEur;

class CurrencyRepository extends BaseRepository
{
    /**
     * @param UsdEur|null $usdEur
     */
    public function updateUsdEur(?UsdEur $usdEur)
    {
        if ($usdEur) {
            $ids = $this->getConnection()->insertQuery(
                'INSERT INTO
                  usd_euro (`date`, `rate`)
                VALUES (?, ?)
                ON DUPLICATE KEY
                UPDATE `rate`=?
                ',
                $usdEur->getDate(),
                $usdEur->getRate(),
                $usdEur->getRate()
            );
            
            if ($ids && $usdEur->getId() === 0) {
                $usdEur->__setId((int)$ids[0]);
            }
        }
    }
    
    /**
     * @return null|UsdEur
     */
    public function getLatestUsdEur(): ?UsdEur
    {
        $usdEurs = $this->selectModel(
            UsdEur::class,
            'usd_euro',
            [],
            [],
            ['date' => 'desc'],
            1
        );
    
        if ($usdEurs) {
            return $usdEurs[0];
        }
    
        return null;
    }
    
    /**
     * @return array
     */
    public function findAllCurrencies(): array
    {
        return $this->selectModel(
            Currency::class,
            'currency'
        );
    }
    
    /**
     * @param string $symbol
     * @return null|Currency
     */
    public function findCurrencyWithSymbol(string $symbol): ?Currency
    {
        $currencyData = $this->selectModel(
            Currency::class,
            'currency',
            [],
            ['symbol' => $symbol],
            [],
            1
        );
        
        if ($currencyData) {
            return $currencyData[0];
        }
        
        return null;
    }
    
    /**
     * @param int $id
     * @return null|Currency
     */
    public function findById(int $id): ?Currency
    {
        $currencyData = $this->selectModel(
            Currency::class,
            'currency',
            [],
            ['id' => $id],
            [],
            1
        );
        
        if ($currencyData) {
            return $currencyData[0];
        }
        
        return null;
    }
    
    /**
     * @param array $ids
     * @return array
     */
    public function findByIds(array $ids): array
    {
        $currencies = $this->selectModel(
            Currency::class,
            'currency',
            [],
            ['id' => $ids]
        );
        
        return $currencies;
    }
    
    /**
     * @param Currency[] ...$currencies
     * @return array
     */
    public function findLatestExchangeRateForCurrency(Currency ...$currencies): array
    {
        /** @var CurrencyExchangeRate $exchangeRate */
        $exchangeRates = [];
    
        $idList = [];
        /** @var Currency $currency */
        foreach ($currencies as $currency) {
            $id = (int)$currency->getId();
            if ($id) {
                $idList[] = $id;
            }
        }
        
        if ($idList) {
            $currencyData = $this->getConnection()->fetchQuery(
                'SELECT t1.*
                            FROM currency_exchange_rate t1
                            WHERE
                             t1.`currency_id` IN ('. implode(',', $idList) .')
                             AND
                             t1.`updated` = (SELECT MAX(t2.`updated`)
                                             FROM currency_exchange_rate t2
                                             WHERE t2.`currency_id` = t1.`currency_id`)
                                             GROUP BY t1.`currency_id`'
            );
            if ($currencyData) {
                $exchangeRates = $this->hydrateArray(CurrencyExchangeRate::class, $currencyData);
            }
        }
        
        
        return $exchangeRates;
    }
    
    /**
     * @param Currency[] ...$currencies
     * @return bool
     */
    public function updateCurrency(Currency ...$currencies): bool
    {
        $split = $this->splitIntoInsertUpdate(...$currencies);
        if ($split[self::UPDATES]) {
            $this->updateModel(
                'currency',
                ['name', 'symbol', 'icon'],
                ['id'],
                ...$split[self::UPDATES]
            );
        }
        
        if ($split[self::INSERTS]) {
            $this->insertModel(
                'currency',
                ['name', 'symbol', 'icon'],
                ...$split[self::INSERTS]
            );
        }
        
        return true;
    }
    
    /**
     * @param CurrencyExchangeRate[] ...$exchangeRates
     * @return bool
     */
    public function updateCurrencyExchangeRate(CurrencyExchangeRate ...$exchangeRates): bool
    {
        $split = $this->splitIntoInsertUpdate(...$exchangeRates);
        if ($split[self::UPDATES]) {
            $this->updateModel(
                'currency_exchange_rate',
                ['btc', 'btc_lowest', 'btc_highest', 'usd', 'usd_lowest', 'usd_highest', 'updated'],
                ['id'],
                ...$split[self::UPDATES]
            );
        }
    
        if ($split[self::INSERTS]) {
            $this->insertModel(
                'currency_exchange_rate',
                ['currency_id', 'btc', 'btc_lowest', 'btc_highest', 'usd', 'usd_lowest', 'usd_highest', 'updated'],
                ...$split[self::INSERTS]
            );
        }
        
        return true;
    }
}
