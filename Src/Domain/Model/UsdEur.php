<?php declare(strict_types=1);


namespace Src\Domain\Model;


class UsdEur extends BaseModel
{
    /**
     * @var string
     */
    private $date = '';
    
    /**
     * @var float
     */
    private $rate = 0.0;
    
    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }
    
    /**
     * @param string $date
     */
    public function setDate(string $date): void
    {
        $this->date = $date;
    }
    
    /**
     * @return double
     */
    public function getRate(): float
    {
        return $this->rate;
    }
    
    /**
     * @param float $rate
     */
    public function setRate(float $rate): void
    {
        $this->rate = $rate;
    }
}