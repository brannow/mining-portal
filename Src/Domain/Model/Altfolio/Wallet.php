<?php declare(strict_types=1);


namespace Src\Domain\Model\Altfolio;


use Src\Domain\Model\BaseModel;

class Wallet extends BaseModel
{
    /**
     * @var int
     */
    private $currency_id = null;
    
    /**
     * @var int
     */
    private $altfolio_id = null;
    
    /**
     * @var string
     */
    private $address = '';
    
    /**
     * @var float
     */
    private $amount = 0.0;
    
    /**
     * @return int
     */
    public function getCurrencyId(): int
    {
        return $this->currency_id;
    }
    
    /**
     * @param int $currency_id
     */
    public function setCurrencyId(int $currency_id): void
    {
        $this->currency_id = $currency_id;
    }
    
    /**
     * @return int
     */
    public function getAltfolioId(): int
    {
        return $this->altfolio_id;
    }
    
    /**
     * @param int $altfolio_id
     */
    public function setAltfolioId(int $altfolio_id): void
    {
        $this->altfolio_id = $altfolio_id;
    }
    
    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }
    
    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }
    
    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }
    
    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }
}