<?php

use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;

class Security extends Plugin
{

    protected $user;

    public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher)
    {

        // Check whether the "auth" variable exists in session to define the active role
        if ($this->session->has('auth_id')) {
            $user_id = $this->session->get('auth_id');
            $this->user = $user_id ? User::findById($user_id) : null;
        }

        // Take the active controller/action from the dispatcher
        $controller = strtolower($dispatcher->getControllerName());
        $action = $dispatcher->getActionName();

        // Redirect to /login if user is not logged in
        if ($controller !== 'sessions' && ! $this->user)
        {
            $this->view->disable();
            return $this->response->redirect('login');
        }

    }

}
