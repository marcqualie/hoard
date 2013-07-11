<?php

namespace Controller;
use Model\User as UserModel;
use Model\Bucket as BucketModel;

class User extends Base\Page
{

    public $user;
    public $apps;
    public $id;

    public function before ()
    {

        $this->id = isset($this->uri[1]) ? $this->uri[1] : false;
        $this->user = UserModel::findById(new \MongoId($this->id));
        if (! $this->user)
        {
            header('Location: /');
            exit;
        }
        $this->set('u', $this->user);

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

            $bucket_id = $this->app->request->get('bucket');
            if (! $bucket_id)
            {
                return $this->alert('You need to select a Bucket', 'danger');
            }
            $bucket = BucketModel::findById($bucket_id);
            if (! $bucket) {
                return $this->alert('Invalid Bucket ID', 'danger');
            }
            $role = $this->app->request->get('role');
            if (! in_array($role, array('read', 'write', 'admin', 'owner'))) {
                return $this->alert('Invalid Role. Please select read, write, admin or owner', 'danger');
            }
            $bucket->addRole($this->id, $role);
            $this->alert('<strong>' . $this->user->email . '</strong> was granted <strong>' . $role . '</strong> permission to <strong>' . $bucket->id . '</strong>');
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
        $this->set('buckets', BucketModel::find());

        // Applications for this user
        $this->set('user_buckets', $this->user->getBuckets());

    }

}
