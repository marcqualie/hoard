<?php

namespace Controller;

class Api extends Base\Page {

    public function req_get ()
    {

        // Get API Method
        if ( ! isset($this->uri[1]))
        {
            return $this->jsonError(500, 'No API Method Specified');
        }
        $api_method = $this->uri[1];

        // TODO: Authenticate

        // Initialize API
        $api_controller_name = '\\Controller\\Api\\' . ucfirst($api_method);
        if ( ! class_exists($api_controller_name))
        {
            return $this->jsonError(404, 'API Method Not Found');
        }
        $api_controller = new $api_controller_name();
        $api_controller->app = $this->app;

        // Execute API Call
        $response = new \Hoard\ApiResponse($api_controller->exec());

        // Display Output
        if ($response->error)
        {
            return $this->jsonError($response->error['code'], $response->error['message']);
        }
        return $this->json(
            $response->data,
            $response->code,
            $response->meta,
            $response->debug
        );

    }

}
