<?php

class ViewerController extends PageController
{
	
	public function before ()
	{
		
		if (!Auth::$id)
		{
			header('Location: /login');
			exit;
		}
		
	}
	
	public function req_get ()
	{
		
		$this->set('title', 'Hoard Viewer');
		
	}
	
}