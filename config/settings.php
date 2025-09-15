<?php

return array_replace_recursive([
    'displayErrorDetails' => true, // set to false in production
    'addContentLengthHeader' => false, // Allow the web server to send the content-length header

    // Method settings
    'method' => [
        'get' => $_GET,
    ],

    // Directory aliases settings
    'directory_alias' => [
        'fonts' => 's/f',
        'icons' => 's/i',
    ],

    // Csrf prefix settings (beta)
    'csrf' => [
        'prefix' => 'csrf__',
        'storage_limit' => 200,
        'strength' => 16,
    ],

    // Renderer settings
    'renderer' => [
        'template_path' => __DIR__ . '/../templates/',
    ],

    // Logger settings
    'logger' => [
        'path' => __DIR__ . '/../logs/app.log',
        'level' => \Psr\Log\LogLevel::DEBUG,
    ],

    // List of webicons
    'icons' => [],

    // List of webfonts
    'fonts' => [],

    // How long should browsers keep generated CSS in cache (in seconds)
    'cssHttpCacheAge' => 2678400,
], require __DIR__ . '/settings-local.php');
