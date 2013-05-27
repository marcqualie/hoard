<?php

namespace Controller;

class Logout extends Base\Page
{

    public function req_get ()
    {

        $this->app->auth->logout();
        header('Location: /');
        exit;

    }

}
