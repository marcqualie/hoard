<?php

date_default_timezone_set('UTC');

try {

    $root_path = dirname(__DIR__);
    $app_path = $root_path . '/app';

    // Register an autoloader
    $loader = new Phalcon\Loader();
    $loader->registerDirs(array(
        $app_path . '/controllers/',
        $app_path . '/models/',
        $app_path . '/plugins/',
    ))->register();

    // Create a DI
    $di = require $app_path . '/di.php';

    // Handle the request
    $app = new \Phalcon\Mvc\Application($di);
    echo $app->handle()->getContent();

}

// Catch any crazy exceptions
catch (\Phalcon\Exception $e) {
    echo "PhalconException: ", $e->getMessage();
}
