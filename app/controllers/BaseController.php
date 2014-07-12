<?php

class BaseController extends Phalcon\Mvc\Controller
{

    protected $mainLayout = 'index';
    protected $authUser;

    public function initialize()
    {
        $this->view->setMainView('layouts/' . $this->mainLayout);
        if ($this->session->has('auth_id')) {
            $user_id = $this->session->get('auth_id');
            $this->authUser = User::findById($user_id);
            $this->view->setVar('authUser', $this->authUser);
        }
    }

}
