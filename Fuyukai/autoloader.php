<?php declare(strict_types=1);

function __fuyukai_autoloader($class): void {
    // starts every time at root directory
    $systemPath = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR;
    $segments = explode('\\', $class);
    $systemPath .= implode(DIRECTORY_SEPARATOR, $segments) . '.php';
    if (file_exists($systemPath) && !is_dir($systemPath)) {
        include $systemPath;
    } else {
        //die('error: 0x000002 (' . $systemPath . ') ');
    }
}

if(!spl_autoload_register('__fuyukai_autoloader')) {
    die('error: 0x000001');
}
