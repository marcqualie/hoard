<?php

namespace Controller;

class Login extends Base\Page
{

	public function req_post ()
	{

		$login = $this->app->auth->login($this->app->request->get('email'), $this->app->request->get('password'));
		if (isset($login['error']))
		{
			$this->alert($login['message'], 'danger');
		}
		else
		{
			header('Location: /');
			exit;
		}

	}

	public function req_get ()
	{

		$this->set('title', 'Hoard - Login');

	}

}
