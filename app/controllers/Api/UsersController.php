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

}
