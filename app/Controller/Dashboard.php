<?php

namespace Controller;

class Dashboard extends Base\Page
{

    public function before ()
    {
        if ( ! $this->isLoggedIn()) {
            header('Location: /login/');
            exit;
        }
        header('Location: /buckets/');
        exit;
    }

}
