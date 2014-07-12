<?php

class BaseController extends Phalcon\Mvc\Controller
{

    protected $mainLayout = 'index';

    public function initialize()
    {
        $this->view->setMainView('layouts/' . $this->mainLayout);
    }

}
