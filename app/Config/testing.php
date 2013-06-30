<?php

return $extend('default', array(
    'app.name' => 'Hoard Test Server',
    'mongo.server' => 'mongodb://127.0.0.1:27017',
    'mongo.options' => array(
        'db' => 'hoard_test',
        'connect' => true
    )
));
