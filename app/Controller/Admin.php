<?php

namespace Controller;

class Admin extends Base\Page
{

    public function before ()
    {
        if ( ! $this->app->auth->isAdmin())
        {
            header('Location: ' . ($this->app->auth->id ? '/account' : '/login'));
            exit;
        }
    }

    public function req_post ()
    {

        if ($_POST['action'] === 'create-user')
        {
            $email = strtolower($_POST['email']);
            if ( ! $email)
            {
                return $this->alert('You need to specify a valid email', 'danger');
            }
            $password = $_POST['password'];
            if ( ! $password)
            {
                return $this->alert('You cannot add an emoty password', 'danger');
            }
            $token = uniqid();
            $user = $this->app->mongo->selectCollection('user')->findOne(array('email' => $email));
            if (isset($user['_id']))
            {
                return $this->alert('There is already a user with that email address', 'danger');
            }
            $data = array(
                'email' => $email,
                'password' => $this->app->auth->password($password),
                'token' => $token,
                'created' => new \MongoDate(),
                'updated' => new \MongoDate()
            );
            $id = $this->app->mongo->selectCollection('user')->insert($data);
            $this->alert('User created', 'success');

        }

    }

    public function after ()
    {

        // Get all Applications
        $cursor = $this->app->mongo->selectCollection('app')->find();
        $this->set('buckets', $cursor);

        // Get All users
        $cursor = $this->app->mongo->selectCollection('user')->find();
        $this->set('users', $cursor);

        // Title
        $this->title = 'Hoard Admin';

    }

}
