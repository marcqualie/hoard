<?php

namespace Controller;

class Account extends Base\Page {

    public $view = 'Account/Dashboard';

    public function req_get ()
    {

        // Get API Keys
        $apikeys = array(
            array(
                'apikey' => 'demokey1',
                'secret' => 'demosecret1',
                'created' => new \MongoDate()
            ),
            array(
                'apikey' => 'demokey2',
                'secret' => 'demosecret2',
                'created' => new \MongoDate()
            )
        );
        $this->set('apikeys', $apikeys);

    }

}
