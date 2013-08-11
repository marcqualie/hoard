<?php

namespace Hoard;
use Symfony\Component\HttpFoundation\Request;

class Application
{

    public static $version = '0.0.1';
    public static $app;

    public $env = 'development';
    public $mongo;
    public $config;
    public $request;
    public $router;
    public $auth;

    public function __construct ()
    {
        $this->router = new Router();
        $this->request = Request::createFromGlobals();
        $this->auth = new Auth($this);
        if (! self::$app) {
            self::$app = $this;
        }
    }

    /**
     * Main call to application
     */
    public function run ()
    {

        // Authentication
        $this->auth->check();

        // Serve page and return response instance
        return $this->router->render($this);

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
            if (0 == error_reporting()) {
                return;
            }
            throw new \ErrorException($message, 0, $code, $file, $line);
        });
    }

    /**
     * Redirect
     */
    public function redirect ($url)
    {
        header('Location: ' . $url);
        exit;
    }

}
