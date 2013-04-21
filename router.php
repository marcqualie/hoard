<?php

// Force error displaying for development mode
ini_set('display_errors', 'On');
error_reporting(E_ALL);

// Serve static files out of CLI Server
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
        'gif' => 'image/gif'
    );
    if (array_key_exists($extension, $mimes))
    {
        header('Content-Type: ' . $mimes[$extension]);
    }
    readfile($file);
    exit;
}

// Include the rest of the framework
include __DIR__ . '/public/index.php';
