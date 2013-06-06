<?php

ignore_user_abort(true);
$app = include dirname(__DIR__) . '/bootstrap.php';

// Serve static files out of CLI Server
if (php_sapi_name() === 'cli-server')
{

    $path = $_SERVER['REQUEST_URI'];
    $file = __DIR__ . '/public' . $path;
    $extension = pathinfo($file, PATHINFO_EXTENSION);
    if (file_exists($file) && $extension)
    {
        $mimes = array(
            'css' => 'text/css',
            'js' => 'text/javascript',
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'eot' => 'application/vnd.ms-fontobject',
            'ttf' => 'application/x-font-ttf',
            'woff' => 'application/x-font-woff',
            'svg' => 'application/xml+svg'
        );
        if (array_key_exists($extension, $mimes))
        {
            header('Content-Type: ' . $mimes[$extension]);
        }
        else
        {
            header('Content-Type: text/plain');
        }
        readfile($file);
        exit;
    }
}

// Run Application
$app->run();
