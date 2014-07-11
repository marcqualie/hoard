<?php

try {

    $root_path = dirname(__DIR__);
    $app_path = $root_path . '/app';

    // Register an autoloader
    $loader = new Phalcon\Loader();
    $loader->registerDirs(array(
        $app_path . '/controllers/',
        $app_path . '/models/'
    ))->register();

    // Create a DI
    $di = new Phalcon\DI\FactoryDefault();
    require $app_path . '/di.php';

    // Routing
    $di->set('router', require '../app/router.php');

    // Setup the view component
    $di->set('view', function() use ($app_path) {
        $view = new \Phalcon\Mvc\View();
        $view->setViewsDir($app_path . '/views/');
        return $view;
    });

    // Handle the request
    $app = new \Phalcon\Mvc\Application($di);
    echo $app->handle()->getContent();

}

// Catch any crazy exceptions
catch (\Phalcon\Exception $e) {
    echo "PhalconException: ", $e->getMessage();
}
