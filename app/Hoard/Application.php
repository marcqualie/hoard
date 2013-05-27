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


    /**
     * Error and Exception Handler
     */
    public function error ($callback)
    {
        set_exception_handler(function (\Exception $e) use ($callback) {
            $callback($e, $e->getCode());
        });
        set_error_handler(function ($code, $message, $file, $line) {
            if (0 == error_reporting())
            {
                return;
            }
            throw new \ErrorException($message, 0, $code, $file, $line);
        });
    }

}
