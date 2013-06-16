<?php

namespace Controller;
use Model;

class Account extends Base\Page {

    public $view = 'Account/Dashboard';

    public function req_get ()
    {

        // Do Stuff
        $action = $this->app->request->get('action');
        switch ($action) {
            case 'create-apikey':
                $this->app->auth->user->createApiKey();
                break;
        }

        // Get API Keys
        $apikeys = $this->app->auth->user->apikeys;
        $this->set('apikeys', $apikeys);

    }

}
