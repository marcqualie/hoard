<?php

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
    $di = new Phalcon\DI\FactoryDefault();
    require $app_path . '/di.php';

    // Override Dispatcher
    $di->set('dispatcher', function() use ($di) {

        // Obtain the standard eventsManager from the DI
        $eventsManager = $di->getShared('eventsManager');

        // Instantiate the Security plugin
        $security = new Security($di);

        // Listen for events produced in the dispatcher using the Security plugin
        $eventsManager->attach('dispatch', $security);

        $dispatcher = new Phalcon\Mvc\Dispatcher();

        // Bind the EventsManager to the Dispatcher
        $dispatcher->setEventsManager($eventsManager);

        return $dispatcher;
    });

    // Routing
    $di->set('router', require '../app/router.php');

    // Start the session the first time a component requests the session service
    $di->set('session', function() {
        $session = new Phalcon\Session\Adapter\Files();
        $session->start();
        return $session;
    });

    // Make flash messages use the sessions
    $di->set('flashSession', function() {
        return new \Phalcon\Flash\Session([
            'error' => 'alert alert-danger',
            'success' => 'alert alert-success',
            'notice' => 'alert alert-info'
        ]);
    });

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
