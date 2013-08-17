<?php

namespace Controller;
use Model\User;

class Api extends Base\Page
{
    public function req_get ()
    {

        // Get API Method
        if (! isset($this->uri[1])) {
            return $this->jsonError('No API Method Specified', 500);
        }
        $api_method = $this->uri[1];

        // Authenticate using API Keys
        $apikey = $this->app->request->get('apikey');
        if (! $apikey) {
            return $this->jsonError('API Key is required', 401);
        }
        $user_collection = $this->app->mongo->selectCollection(User::$collection);
        $apikey_isvalid = $user_collection->find(
            array(
                ('apikeys.' . $apikey . '.active') => true
            )
        )->count() > 0;
        if (! $apikey_isvalid) {
            return $this->jsonError('Invalid API Key', 401);
        }

        // Initialize API
        $api_controller_name = '\\Controller\\Api\\' . ucfirst($api_method);
        if ( ! class_exists($api_controller_name)) {
            return $this->jsonError('API Method Not Found', 404);
        }
        $api_controller = new $api_controller_name();
        $api_controller->app = $this->app;

        // Execute API Call
        $response = new \Hoard\ApiResponse($api_controller->exec());

        // Display Output
        if ($response->error) {
            return $this->jsonError($response->error['message'], $response->error['code']);
        }

        // TODO: Log request
        $user_collection->update(
            array(
                ('apikeys.' . $apikey . '.active') => true
            ),
            array(
                '$inc' => array(
                    ('apikeys.' . $apikey . '.requests') => 1
                )
            )
        );

        // Return Response Object
        return $this->json(
            $response->data,
            $response->code,
            $response->meta,
            $response->debug
        );

    }

}
