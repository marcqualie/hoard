<?php

namespace Console;
use Symfony\Component\Console\Application as SymfonyApplication;

class Application extends SymfonyApplication
{
    public function __construct ($name, $version, $app)
    {
        $this->app = $app;
        parent::__construct($name, $version);
    }

    /**
     * Add commands and add $app dependency
     */
    public function addCommands(array $commands)
    {
        foreach ($commands as $command_name) {
            $class_name = '\\Console\\' . $command_name;
            $class = new $class_name($this->app);
            $this->add($class);
        }
    }

}
