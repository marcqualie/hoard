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
                $this->app->redirect('/account/');
                break;
            case 'update-apikey':
                $this->app->auth->user->updateApiKey(array(
                    'id' => $this->app->request->get('id'),
                    'name' => $this->app->request->get('name'),
                    'active' => $this->app->request->get('active'),
                ));
                return $this->json(array('ok' => 1));
                break;
        }

        // Get API Keys
        $apikeys = $this->app->auth->user->apikeys;
        $this->set('apikeys', $apikeys);

    }

}
