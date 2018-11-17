<?php declare(strict_types=1);


namespace Fuyukai\Userspace\Parser;


abstract class JsonParser extends BaseParser
{
    protected const CONTENT_TYPE = 'application/json';
    
    /**
     * @param string $rawResult
     * @param int $id
     * @return array
     */
    protected static function parseResult(string $rawResult, int $id): array
    {
        $assocArray = json_decode($rawResult, true);
        if ($assocArray) {
            
            if (!is_array($assocArray)) {
                $assocArray = [
                    'result' => $assocArray
                ];
            }
            
            return $assocArray;
        }
        
        return [];
    }
}