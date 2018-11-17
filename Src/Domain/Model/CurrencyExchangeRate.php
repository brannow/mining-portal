<?php declare(strict_types=1);


namespace Src\Domain\Model;


use Src\Domain\Repository\BaseRepository;

class CurrencyExchangeRate extends BaseModel
{
    /**
     * @var float
     */
    private $btc = 0.0;
    
    /**
     * @var float
     */
    private $btcLowest = 0.0;
    
    /**
     * @var float
     */
    private $btcHighest = 0.0;
    
    /**
     * @var float
     */
    private $usd = 0.0;
    
    /**
     * @var float
     */
    private $usdLowest = 0.0;
    
    /**
     * @var float
     */
    private $usdHighest = 0.0;
    
    /**
     * @var string
     */
    private $updated = '';
    
    /**
     * @var \DateTime|null
     */
    private $updatedDateTime = null;
    
    /**
     * @var int
     */
    private $currencyId = 0;
    
    /**
     * @return float
     */
    public function getBtc(): float
    {
        return $this->btc;
    }
    
    /**
     * @param float $btc
     */
    public function setBtc(float $btc): void
    {
        $this->btc = $btc;
    }
    
    /**
     * @return float
     */
    public function getBtcLowest(): float
    {
        return $this->btcLowest;
    }
    
    /**
     * @param float $btcLowest
     */
    public function setBtcLowest(float $btcLowest): void
    {
        $this->btcLowest = $btcLowest;
    }
    
    /**
     * @return float
     */
    public function getBtcHighest(): float
    {
        return $this->btcHighest;
    }
    
    /**
     * @param float $btcHighest
     */
    public function setBtcHighest(float $btcHighest): void
    {
        $this->btcHighest = $btcHighest;
    }
    
    /**
     * @return float
     */
    public function getUsd(): float
    {
        return $this->usd;
    }
    
    /**
     * @param float $usd
     */
    public function setUsd(float $usd): void
    {
        $this->usd = $usd;
    }
    
    /**
     * @return float
     */
    public function getUsdLowest(): float
    {
        return $this->usdLowest;
    }
    
    /**
     * @param float $usdLowest
     */
    public function setUsdLowest(float $usdLowest): void
    {
        $this->usdLowest = $usdLowest;
    }
    
    /**
     * @return float
     */
    public function getUsdHighest(): float
    {
        return $this->usdHighest;
    }
    
    /**
     * @param float $usdHighest
     */
    public function setUsdHighest(float $usdHighest): void
    {
        $this->usdHighest = $usdHighest;
    }
    
    /**
     * @return \DateTime
     */
    public function getUpdated(): string
    {
        return $this->updated;
    }
    
    /**
     * @param string $updated
     * @param bool $preventObjectGeneration
     */
    public function setUpdated(string $updated, bool $preventObjectGeneration = false): void
    {
        $this->updated = $updated;
        if ($updated && !$preventObjectGeneration) {
            $this->updatedDateTime = \DateTime::createFromFormat(BaseRepository::MYSQL_DATETIME_FORMAT, $updated);
        }
    }
    
    /**
     * @return \DateTime|null
     */
    public function getUpdatedDateTime(): ?\DateTime
    {
        return $this->updatedDateTime;
    }
    
    /**
     * @return int
     */
    public function getCurrencyId(): int
    {
        return $this->currencyId;
    }
    
    /**
     * @param int $currencyId
     */
    public function setCurrencyId(int $currencyId): void
    {
        $this->currencyId = $currencyId;
    }
}