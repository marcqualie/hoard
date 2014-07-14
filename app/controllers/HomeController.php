<?php

class HomeController extends BaseController
{

    public function indexAction()
    {
        $this->view->pick('home/index');
    }

}
