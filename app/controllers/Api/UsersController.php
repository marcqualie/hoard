<?php

namespace Api;

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
        $this->respondWith([
            [
                'id' => 1,
                'name' => 'Marc Qualie',
            ]
        ]);
    }

}
