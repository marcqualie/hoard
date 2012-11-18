<?php

class LoginController extends PageController
{
	
	public function req_post ()
	{
		
		$login = Auth::login($_POST['email'], $_POST['password']);
		if (isset($login['error']))
		{
			$this->alert($login['message'], 'danger');
		}
		else
		{
			header('Location: /apps/');
			exit;
		}
		
	}
	
	public function req_get ()
	{
		
		$this->set('title', 'Hoard - Login');

	}
	
}