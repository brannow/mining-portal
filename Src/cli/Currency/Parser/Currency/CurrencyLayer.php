<?php declare(strict_types=1);


namespace Src\cli\Currency\Parser\Currency;


use Config\Config;
use Fuyukai\Userspace\Parser\JsonParser;
use Src\Domain\Model\UsdEur;

class CurrencyLayer extends JsonParser
{
    public const CUR_USD = 'USD';
    public const CUR_EUR = 'EUR';
    
    private const URL = 'http://apilayer.net/api/live';
    
    /**
     * @param string $base
     * @param string $exchange
     * @return UsdEur|null
     */
    public static function latest(string $base = self::CUR_USD, string $exchange = self::CUR_EUR): ?UsdEur
    {
        $result = static::execute(
            static::URL,
            [
                'access_key' => Config::getConfigEntry(Config::API_CL_KEY),
                'source' => $base,
                'currencies' => $exchange
            ]
        );
        
        if (
            !empty($result['quotes']) && isset($result['timestamp']) &&
            isset($result['success']) && $result['success'] === true &&
            isset($result['source']) && $result['source'] === $base) {
        
            $exchanges = $result['quotes'];
            if (!empty($exchanges[$base.$exchange])) {
                $rate = (float)($exchanges[$base.$exchange]);
                $timestamp = (int)($result['timestamp']);
                $date = date('Y-m-d', $timestamp);
                
                $usdEur = new UsdEur();
                $usdEur->setDate($date);
                $usdEur->setRate($rate);
                return $usdEur;
            }
        }
        
        return null;
    }
}