<?php

class LogoutController extends PageController
{
	
	public function req_get ()
	{
		
		Auth::logout();
		header('Location: /');
		exit;
		
	}
	
}