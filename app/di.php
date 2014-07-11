<?php

// Database Connection
$di->set('mongo', function() {
    $mongo = new MongoClient("mongodb://localhost:27017");
    return $mongo->selectDB("hoard-beta");
}, true);
$di->set('collectionManager', function(){
    return new Phalcon\Mvc\Collection\Manager();
}, true);
