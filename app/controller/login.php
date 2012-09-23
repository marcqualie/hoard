<?php

class LoginController extends PageController
{
	
	public function req_post ()
	{
		
		$login = Auth::login($_POST['email'], $_POST['password']);
		if ($login['error'])
		{
			$this->alert($login['message'], 'danger');
		}
		else
		{
			//Router::location('/account');
			header('Location: /apps');
			exit;
		}
		
	}
	
	public function req_get ()
	{
		
	}
	
}