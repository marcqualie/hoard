<?php

namespace Hoard;

class Router {

    public function render ($app, $method = null, array $vars = array())
    {

        // Detect Page Method
        if ($method === null)
        {
            $uri_full = 'http://' . $app->request->server->get('HTTP_HOST') . $app->request->server->get('REQUEST_URI');
            $uri_path = parse_url($uri_full, PHP_URL_PATH);
            $uri_parts = explode('/', $uri_path);
            array_shift($uri_parts);
            $method = ! empty($uri_parts[0]) ? $uri_parts[0] : 'dashboard';
        }
        else
        {
            $uri_parts = array('error');
        }

        // Check if method exists
        $class = 'Controller\\' . ucfirst($method);
        if ( ! class_exists($class))
        {
            $method = 'error';
            header('HTTP/1.1 404 Not Found');
            $class = 'Controller\\Error';
        }

        // Initialize class
        $page = new $class($app);
        $page->uri = $uri_parts;
        $page->var = $vars;
        $page->view = $method;
        $page->controller = $method;
        $page->config = $app->config;
        $page->before();
        $page->{ 'req_' . $app->request->getMethod() }();
        $page->after();
        $page->template = 'Page/' . ucfirst($page->view) . '.twig';

        // Sort out variables
        $twig_variables = $page->var;
        $twig_variables['page'] = $page;
        $twig_variables['app'] = $app;
        $twig_variables['auth'] = $app->auth;
        $twig_variables['user'] = $app->auth->user;

        // Twig Setup
        $loader = new \Twig_Loader_Filesystem(dirname(dirname(__DIR__)) . '/app/View');
        $twig = new \Twig_Environment($loader, array(
//            'cache' => '/tmp',
        ));
        $twig->addFilter(new \Twig_SimpleFilter('normalize_bytes', function ($bytes, $precision = 2, $html = false) {
                return \Utils::normalize_bytes($bytes, $precision, $html);
            }, array(
                'is_safe' => array('html')
            ))
        );
        $twig->addFilter(new \Twig_SimpleFilter('json', function ($string) {
            return json_encode($string, JSON_PRETTY_PRINT);
        }));
        echo $twig->render('Layout/Default.twig', $twig_variables);

    }


}
