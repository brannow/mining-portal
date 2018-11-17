<?php declare(strict_types=1);

if (php_sapi_name() === 'apache2handler') {
    $compute = function() {
        // update doc-root for autoloader
        $currentRootDirectory = realpath(dirname(__FILE__));
        $_SERVER['DOCUMENT_ROOT'] = $currentRootDirectory;
        require $currentRootDirectory . DIRECTORY_SEPARATOR . 'Fuyukai' . DIRECTORY_SEPARATOR . 'autoloader.php';
        
        $kernel = new \Fuyukai\Kernel();
        $kernel->execute();
        $kernel->shutdown();
    };
    $compute();
    unset($compute);
}