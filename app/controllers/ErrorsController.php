<?php

use Phalcon\Mvc\View;

class ErrorsController extends BaseController
{

    public function initialize()
    {
        $this->view->disableLevel(array(
            View::LEVEL_LAYOUT => true,
            View::LEVEL_MAIN_LAYOUT => true
        ));
    }

    public function notFoundAction()
    {
        $this->view->pick('errors/notFound');
    }

}
