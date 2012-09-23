<?php

class AdminController extends PageController
{
	
	public function before ()
	{
		if (!Auth::$admin)
		{
			redirect(Auth::$id ? '/account' : '/login');
		}
	}
	
	public function req_post ()
	{
		
		if ($_POST['action'] === 'create-user')
		{
			$email = strtolower($_POST['email']);
			if (!$email)
			{
				return $this->alert('You need to specify a valid email', 'danger');
			}
			$password = $_POST['password'];
			if (!$password)
			{
				return $this->alert('You cannot add an emoty password', 'danger');
			}
			$token = uniqid();
			$user = MongoX::selectCollection('user')->findOne(array('email' => $email));
			if ($user['_id'])
			{
				return $this->alert('There is already a user with that email address', 'danger');
			}
			$id = MongoX::selectCollection('user')->insert(array(
				'email' => $email,
				'password' => Auth::password($password),
				'token' => $token,
				'created' => new MongoDate(),
				'updated' => new MongoDate()
			));
			$this->alert('User created', 'success');
			
		}
		
	}
	
	public function after ()
	{
		
		// Get all Applications
		$cursor = MongoX::selectCollection('app')->find();
		$this->set('apps', $cursor);
		
		// Get All users
		$cursor = MongoX::selectCollection('user')->find();
		$this->set('users', $cursor);
		
	}
	
}