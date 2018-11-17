<?php declare(strict_types=1);

if (php_sapi_name() === 'cli' || php_sapi_name() === 'cgi-fcgi') {
    
    /**
     * @param array $argv
     * @param int $argc
     * @return array
     */
    function parseArguments(array $argv, int $argc): array
    {
        $list = [];
        for ($i = 1; $i < $argc; $i = $i + 2) {
            
            $rawKey = $argv[$i];
            $key = str_replace('-', '', trim($rawKey));
            
            $value = '';
            if (isset($argv[$i+1])) {
                $rawValue = $argv[$i+1];
                $value = trim($rawValue);
            }
            
            $list[$key] = $value;
        }
        
        return $list;
    }
    
    $compute = function(array $argv = [], int $argc = 0) {
        $arguments = parseArguments($argv, $argc);
        if (!empty($arguments['key']) && !empty($arguments['cmd'])) {
            $key = (string)$arguments['key'];
            $cmd = (string)$arguments['cmd'];
            unset($arguments['key']);
            unset($arguments['cmd']);
            
            // key can be hard coded cuz it's only on our local system used, if someone has access to the file direcly
            // and can execute a shell command, the key is worthless,
            // it's only for my peace of mind ;)
            if ($key === 'K3Pgjt6794A47qe43y8X') {
                // update doc-root for autoloader
                $currentRootDirectory = realpath(dirname(__FILE__));
                $_SERVER['DOCUMENT_ROOT'] = $currentRootDirectory;
                require $currentRootDirectory . DIRECTORY_SEPARATOR . 'Fuyukai' . DIRECTORY_SEPARATOR . 'autoloader.php';
                $kernel = new \Fuyukai\Kernel(\Fuyukai\Kernel::MODE_CLI);
                $kernel->setCLIRoutePath($cmd);
                $kernel->execute();
                $kernel->shutdown();
            }
        }
    };
    $compute($argv, $argc);
    unset($compute);
}
