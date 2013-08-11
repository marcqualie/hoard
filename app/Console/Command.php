<?php

namespace Console;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Model\User;

class Command extends SymfonyCommand
{
    public $app;
    public $mongo;
    public $user;
    public $apikey;

    public function __construct($app)
    {
        $this->app = $app;
        $this->mongo = $app->mongo;
        parent::__construct();
        $this->assertCliUser();
    }

    /**
     * Create CLI User (Requires API Key Access)
     */
    public function assertCliUser()
    {
        $this->user = User::findOne(array(
            'email' => 'cli@hoardhq.com'
        ));
        if (! $this->user) {
            $this->user = User::create(array(
                'email' => 'cli@hoardhq.com'
            ));
            $this->user->createApiKey();
        }
        $apikeys = array_keys($this->user->apikeys);
        $this->apikey = isset($apikeys[0]) ? $apikeys[0] : false;
        if (! $this->apikey) {
            $this->user->createApiKey();
            $this->assertCliUser();
        }
    }

}
