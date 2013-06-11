<?php

namespace Controller\Base;

class Page
{

    public $uri = array();
    public $params = array();
    public $app;
    public $view;

    public $title = 'Hoard';
    public $controller = 'buckets';
    public $template = 'Page/Error.twig';

    // Requests
    public function before () { }
    public function req_get () { }
    public function req_head () { return $this->req_get(); }
    public function req_post () { return $this->req_get(); }
    public function after () { }

    // Variables
    public $var = array();
    public function set ($key, $value)
    {
        $this->var[$key] = $value;
    }
    public function get ($key)
    {
        return $this->var[$key];
    }

    // Initialize
    public function __construct($app)
    {
        $this->app = $app;
    }

    // Set page alert
    public $alert_data = array();
    public function alert ($str = null, $type = 'info')
    {
        if ($str === null)
        {
            return $this->alert_data;
        }
        $this->alert_data = array(
            'message' => $str,
            'type' => $type
        );
    }


    /**
     * Authentication
     */
    public function isLoggedIn()
    {
        return $this->app->auth->id ? true : false;
    }


    /**
     * JSON Output
     */
    public function jsonError ($code = 500, $message = 'Application Error')
    {
        return $this->json(array(), $code, array(
            'error' => $message
        ));
    }
    public function json (array $data, $code = 200, array $meta = array())
    {
        header('Content-Type: text/json');
        $out = array();
        $out['time'] = 0;
        if ($meta)
        {
            $out['meta'] = $meta;
        }
        $out['data'] = $data;
        echo json_encode($out);
        exit;
    }

}
