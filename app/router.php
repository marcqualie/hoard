<?php

$router = new \Phalcon\Mvc\Router\Annotations(false);

// 404
$router->notFound([
    "controller" => "errors",
    "action" => "notFound"
]);

// Default Route
$router->setDefaults([
    'action' => 'index'
]);
$router->add('/', 'Home::index');

// API resources
$router->addResource('Api\Users', '/api/users');

// Return instance for dependency injection
return $router;
