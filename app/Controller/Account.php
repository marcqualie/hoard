<?php

namespace Controller;

class Account extends Base\Page {

    public $view = 'Account/Dashboard';

    public function req_get ()
    {

        // Get API Keys
        $apikeys = $this->app->auth->user->apikeys;
        $this->set('apikeys', $apikeys);

    }

}
