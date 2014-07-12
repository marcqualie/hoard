<?php

$di = new Phalcon\DI\FactoryDefault();

// Database Connection
$di->set('mongo', function() {
    $mongo = new MongoClient("mongodb://localhost:27017");
    return $mongo->selectDB("hoard-beta");
}, true);
$di->set('collectionManager', function(){
    return new Phalcon\Mvc\Collection\Manager();
}, true);

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
$di->set('router', require $app_path . '/router.php');

// Start the session the first time a component requests the session service
$di->set('session', function() {
    $session = new Phalcon\Session\Adapter\Files();
    $session->start();
    return $session;
});

// Make flash messages use the sessions
$di->set('flashSession', function() {
    return new Phalcon\Flash\Session([
        'error' => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice' => 'alert alert-info'
    ]);
});

// Use Volt for views
$di->set('volt', function($view, $di) use ($app_path) {
    $volt = new Phalcon\Mvc\View\Engine\Volt($view, $di);
    $volt->setOptions([
        'compileAlways' => true,
        'compiledPath' => $app_path . '/cache/volt/',
    ]);
    return $volt;
});
$di->set('view', function() use ($di, $app_path) {
    $view = new Phalcon\Mvc\View;
    $view->setViewsDir($app_path . '/views/');
    $view->registerEngines([
        '.volt' => 'volt',
    ]);
    return $view;
});

// Return instance
return $di;
