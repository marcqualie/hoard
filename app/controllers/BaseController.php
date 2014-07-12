<?php

class BaseController extends Phalcon\Mvc\Controller
{

    protected $authUser;

    public function initialize()
    {
        if ($this->session->has('auth_id')) {
            $user_id = $this->session->get('auth_id');
            $this->authUser = User::findById($user_id);
            $this->view->setVar('authUser', $this->authUser);
        }
    }

}
