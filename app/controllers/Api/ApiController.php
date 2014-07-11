<?php

namespace Api;

use Phalcon\Mvc\Controller as BaseController;

class ApiController extends BaseController
{

    public function respondWith($resource)
    {
        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setJsonContent([
            'meta' => [],
            'resource' => $resource,
        ]);
        $this->response->send();
    }

}
