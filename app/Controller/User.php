<?php

namespace Controller;

class User extends Base\Page
{

    public $user;
    public $apps;
    public $id;

    public function before ()
    {

        $id = $this->uri[1];
        $this->id = $id;
        $user = $this->app->mongo->selectCollection('user')->findOne(array(
            '_id' => new \MongoId($id)
        ));
        if ( ! $user)
        {
            header('Location: /');
            exit;
        }
        $this->user = $user;

    }

    public function req_get ()
    {

        $action = isset($_GET['action']) ? $_GET['action'] : false;

        // Remove User Access
        if ($action === 'revoke-app-access')
        {
            $bucket = $_GET['bucket'];
            if ( ! $bucket)
            {
                return $this->alert('You need to select an appkey', 'danger');
            }
            $app = $this->app->mongo->selectCollection('app')->findOne(array('appkey' => $bucket));
            if ( ! $app['_id'])
            {
                return $this->alert('Invalid Application', 'danger');
            }
            $this->app->mongo->selectCollection('app')->update(
                array('appkey' => $bucket),
                array('$unset' => array('roles.' . $this->id => 1))
            );
            $this->alert('User permission was revoked from <strong>' . $app['name'] . '</strong>');
        }

    }

    public function req_post ()
    {

        $action = isset($_POST['action']) ? $_POST['action'] : false;

        // Grant user access to an application
        if ($action === 'grant-app-access')
        {

            $bucket = $_POST['bucket'];
            if (!$bucket)
            {
                return $this->alert('You need to select an appkey', 'danger');
            }
            $app = $this->app->mongo->selectCollection('app')->findOne(array('appkey' => $bucket));
            if ( ! $app['_id'])
            {
                return $this->alert('Invalid Application', 'danger');
            }
            $role = $_POST['role'];
            if (!in_array($role, array('read', 'write', 'admin', 'owner')))
            {
                return $this->alert('Invalid Role. Please select read, write, admin or owner', 'danger');
            }
            $this->app->mongo->selectCollection('app')->update(
                array('appkey' => $bucket),
                array('$set' => array('roles.' . $this->id => $role))
            );
            $this->alert('<strong>' . $this->user['email'] . '</strong> was granted <strong>' . $role . '</strong> permission to <strong>' . $app['name'] . '</strong>');
        }

        // Change Password
        if ($action === 'change-password')
        {
            $password = $_POST['password'];
            if ( ! $password || strlen($password) < 4)
            {
                return $this->alert('Password must be >= 4 chars', 'danger');
            }
            $password_hash = $this->app->auth->password($password);
            $this->user['password'] = $password_hash;
            $this->app->mongo->selectCollection('user')->update(
                array('_id' => new \MongoId($this->id)),
                array('$set' => array('password' => $password_hash))
            );
            $this->alert('Password updated', 'success');
        }

    }

    public function after ()
    {

        // All Applications
        $cursor = $this->app->mongo->selectCollection('app')->find();
        $this->set('buckets', iterator_to_array($cursor));

        // Applications for this user
        $cursor = $this->app->mongo->selectCollection('app')->find(array(
            '$or' => array(
                array(
                    'roles.' . $this->id => array(
                        '$exists' => 1
                    )
                ),
                array(
                    'roles.all' => array(
                        '$exists' => 1
                    )
                )
            )
        ));
        $this->set('user_buckets', iterator_to_array($cursor));

    }

}
