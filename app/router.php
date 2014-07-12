<?php

$router = new \Phalcon\Mvc\Router\Annotations(false);

// 404
$router->notFound([
    "controller" => "errors",
    "action" => "notFound"
]);

// Default Route
$router->add('/', 'Home::index')->setName('home');

// Standard Controllers
$router->addResource('Sessions', '/sessions');
$router->addGet('/login', 'Sessions::new')->setName('login');
$router->addGet('/logout', 'Sessions::destroy')->setName('logout');

// API resources
$router->addResource('Api\Users', '/api/users');

// Return instance for dependency injection
return $router;
