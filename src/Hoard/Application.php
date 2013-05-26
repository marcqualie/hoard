<?php

namespace Hoard;
use Symfony\Component\HttpFoundation\Request;

class Application {

    public static $version      = '0.0.1';

    public $env = 'development';
    public $mongo;
    public $config;
    public $request;
    public $router;
    public $auth;

    public function __construct ()
    {
        $this->router = new Router();
        // Internal Request
        $this->request = Request::createFromGlobals();
    }


    /**
     * Main call to application
     */
    public function run ()
    {

        // Authentication
        $this->auth = new Auth($this);
        $this->auth->check();

        // Server page
        $this->router->render($this);

    }

}
