<?php

namespace Console;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class Command extends SymfonyCommand {

    public $app;
    public $mongo;

    public function __construct($app)
    {
        $this->app = $app;
        $this->mongo = $app->mongo;
        parent::__construct();
    }

}
