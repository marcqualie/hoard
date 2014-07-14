<?php

use Phalcon\Mvc\View;

class BaseController extends Phalcon\Mvc\Controller
{

    protected $authUser;

    public function initialize()
    {
        // Disable view layers that we don't use
        $this->view->disableLevel(array(
            View::LEVEL_LAYOUT => true,
            View::LEVEL_MAIN_LAYOUT => true
        ));

        // Pre-load user when logged in
        if ($this->session->has('auth_id')) {
            $user_id = $this->session->get('auth_id');
            $this->authUser = User::findById($user_id);
            $this->view->setVar('authUser', $this->authUser);
        }

        // Create a server metrics instance
        $this->view->setVar('serverMetrics', new HoardUtils\ServerMetrics);

    }

}
