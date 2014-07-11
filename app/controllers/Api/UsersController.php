<?php

namespace Api;

use User;

/**
 * @RoutePrefix("/api/users")
 */
class UsersController extends ApiController
{

    /**
     * @Get("/")
     */
    public function indexAction()
    {
        $users = User::find();
        $this->respondWith($users);
    }

    /**
     * @Get("/{id:[a-zA-Z0-9]+}")
     */
    public function showAction($id)
    {
        $users = User::findById($id);
        $this->respondWith($users);
    }

}
