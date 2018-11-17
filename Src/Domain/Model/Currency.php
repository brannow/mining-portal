<?php declare(strict_types=1);


namespace Src\Domain\Model;


class Currency extends BaseModel
{
    /**
     * @var string
     */
    private $name = '';
    
    /**
     * @var string
     *
     * Currency Symbol like Bitcoin = BTC
     * or VTC, XVG, etc
     */
    private $symbol = '';
    
    /**
     * @var string
     */
    private $icon = '';
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
    
    /**
     * @return string
     */
    public function getSymbol(): string
    {
        return $this->symbol;
    }
    
    /**
     * @param string $symbol
     */
    public function setSymbol(string $symbol): void
    {
        $this->symbol = $symbol;
    }
    
    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }
    
    /**
     * @param string $icon
     */
    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }
}