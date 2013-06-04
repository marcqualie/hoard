#!/usr/bin/env php
<?php

include __DIR__ . '/bootstrap.php';
use Console\Application;

$application = new Application(
    'Hoard CLI',
    Hoard\Application::$version,
    $app
);
$application->addCommands(array(
    'ListReports',
    'RunReport',
    'GenerateEvents',
));
$application->run();
