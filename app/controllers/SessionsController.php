<?php

/**
 * @RoutePrefix('/sessions')
 */
class SessionsController extends BaseController
{

    protected $mainLayout = 'basic';

    /**
     * @Get('/new')
     */
    public function newAction()
    {
    }

    /**
     * @Post('/')
     */
    public function createAction()
    {

        // Lookup user base on email address
        $email = trim(strtolower($this->request->getPost('email', 'email')));
        $user = User::findFirst([
            'email' => $email
        ]);

        // Check passwords
        $password = $this->request->getPost('password');
        if (password_verify($password, $user->password)) {
            $this->loginAs($user);
            $this->flashSession->success('Welcome ' . $user->name);
            return $this->response->redirect(['for' => 'home']);
        }

        $this->flashSession->error('Wrong email/password');
        return $this->response->redirect(['for' => 'login']);
    }

    /**
     * @Delete('/')
     */
    public function destroyAction()
    {
        $this->session->remove('auth_id');
        return $this->response->redirect(['for' => 'login']);
    }

    /**
     * Login as user
     */
    protected function loginAs($user)
    {
        $this->session->set('auth_id', $user->getId());
    }

}
