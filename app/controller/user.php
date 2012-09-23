<?php

class UserController extends PageController
{
	
	public $user;
	public $apps;
	public $id;
	
	public function before ()
	{
		
		$id = $this->uri[1];
		$this->id = $id;
		$user = MongoX::selectCollection('user')->findOne(array('_id' => new MongoId($id)));
		if (!$user)
		{
			redirect('/');
		}
		$this->user = $user;
		
	}
	
	public function req_get ()
	{
		
		// Remove User Access
		if ($_GET['action'] === 'revoke-app-access')
		{
			$appkey = $_GET['appkey'];
			if (!$appkey)
			{
				return $this->alert('You need to select an appkey', 'danger');
			}
			$app = MongoX::selectCollection('app')->findOne(array('appkey' => $appkey));
			if (!$app['_id'])
			{
				return $this->alert('Invalid Application', 'danger');
			}
			MongoX::selectCollection('app')->update(
				array('appkey' => $appkey),
				array('$unset' => array('roles.' . $this->id => 1))
			);
			$this->alert('User permission was revoked from <strong>' . $app['name'] . '</strong>');
		}
		
	}
		
	public function req_post ()
	{
		// Grant user access to an application 
		if ($_POST['action'] === 'grant-app-access')
		{
			
			$appkey = $_POST['appkey'];
			if (!$appkey)
			{
				return $this->alert('You need to select an appkey', 'danger');
			}
			$app = MongoX::selectCollection('app')->findOne(array('appkey' => $appkey));
			if (!$app['_id'])
			{
				return $this->alert('Invalid Application', 'danger');
			}
			$role = $_POST['role'];
			if (!in_array($role, array('read', 'write', 'admin', 'owner')))
			{
				return $this->alert('Invalid Role. Please select read, write, admin or owner', 'danger');
			}
			MongoX::selectCollection('app')->update(
				array('appkey' => $appkey),
				array('$set' => array('roles.' . $this->id => $role))
			);
			$this->alert('<strong>' . $this->user['email'] . '</strong> was granted <strong>' . $role . '</strong> permission to <strong>' . $app['name'] . '</strong>');
		}
		
		// Change Password
		if ($_POST['action'] === 'change-password')
		{
			$password = $_POST['password'];
			if (!$password || strlen($password) < 4)
			{
				return $this->alert('Password must be >= 4 chars', 'danger');
			}
			$password_hash = Auth::password($password);
			$this->user['password'] = $password_hash;
			MongoX::selectCollection('user')->update(
				array('_id' => new MongoId($this->id)),
				array('$set' => array('password' => $password_hash))
			);
			$this->alert('Password updated', 'success');
		}
		
	}
	
	public function after ()
	{
		
		// All Applications
		$cursor = MongoX::selectCollection('app')->find();
		$this->set('apps', $cursor);
		
		// Applications for this user
		$cursor = MongoX::selectCollection('app')->find(array('roles.' . $this->id => array('$exists' => 1)));
		$this->set('user_apps', $cursor);
		
	}
	
}