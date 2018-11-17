<?php declare(strict_types=1);


namespace Fuyukai\Userspace\Parser;


use Fuyukai\Userspace\Curl\CurlLoader;

abstract class BaseParser
{
    use CurlLoader;
    
    protected const CONTENT_TYPE = '';
    
    protected const DO_NOT_PARSE_ID = -99;
    
    protected static $resultCache = [];
    
    protected static $customCookieData = [];
    
    /**
     * @param string $url
     * @param array $queryParams
     * @param array $postParams
     * @param string $contentType
     * @param int $id
     * @return array
     */
    protected static function execute(string $url ,array $queryParams = [], array $postParams = [], $contentType = self::CONTENT_TYPE, int $id = 0)
    {
        if ($contentType === self::CONTENT_TYPE) {
            $contentType = static::CONTENT_TYPE;
        }
        
        $rawResult = self::executeRequest($url, $queryParams, $postParams, $contentType, static::$customCookieData);
    
        $data = [];
        if ($id !== self::DO_NOT_PARSE_ID) {
            $data = static::parseResult($rawResult, $id);
        }
        
        unset($rawResult);
        return $data;
    }
    
    /**
     * @param string $rawResult
     * @param int $id
     * @return array
     */
    protected static function parseResult(string $rawResult, int $id): array
    {
        return [];
    }
}