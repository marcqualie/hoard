<?php

use Phalcon\Mvc\View;

class ErrorsController extends BaseController
{

    public function initialize()
    {
        $this->view->disableLevel(View::LEVEL_MAIN_LAYOUT);
    }

    public function notFoundAction()
    {
        $this->view->pick('errors/notFound');
    }

}
