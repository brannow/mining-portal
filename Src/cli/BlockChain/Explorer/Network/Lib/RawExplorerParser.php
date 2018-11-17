<?php declare(strict_types=1);


namespace Src\cli\BlockChain\Explorer\Network\Lib;


abstract class RawExplorerParser extends BlockExplorer implements BlockExplorerInterface
{
    protected const EXPLORER_URL = '';
    
    /**
     * @param string $path
     * @return array
     */
    protected function executePath(string $path): array
    {
        if (static::EXPLORER_URL !== self::EXPLORER_URL && $path) {
            $data = self::execute(trim(static::EXPLORER_URL, '/') . '/' . trim($path, '/'));
            if (isset($data['raw'])) {
                return $this->parseData($data['raw'], $path);
            }
        }
        
        return [];
    }
    
    /**
     * @param string $rawResult
     * @param int $id
     * @return array
     */
    protected static function parseResult(string $rawResult, int $id): array
    {
        return ['raw' => $rawResult];
    }
    
    /**
     * @param string $data
     * @param string $path
     * @return array
     */
    protected function parseData(string $data, string $path): array
    {
        return [];
    }
}