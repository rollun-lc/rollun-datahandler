<?php

if (PHP_SAPI === 'cli-server' && $_SERVER['SCRIPT_FILENAME'] !== __FILE__) {
    return false;
}

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

require 'vendor/autoload.php';

$container = require 'config/container.php';

$filePath = substr($_SERVER['REQUEST_URI'], 1);

if (!$filePath) {
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('example'));

    /** @var SplFileInfo $splFileInfo */
    foreach ($files as $splFileInfo) {
        $filename = basename($splFileInfo->getBasename(), ".php");
        $path = $splFileInfo->getPathInfo()->getPathname();

        if ($splFileInfo->isFile()) {
            echo "<a href='/{$path}/{$filename}'>{$path}/{$splFileInfo->getFilename()}</a><br /><br />";
        }
    }
} else {
    ob_start();
    include_once "{$filePath}.php";
    $content = ob_get_clean();

    echo "<a href='/'>< Back</a><br /><br />";
    echo $content;
}
