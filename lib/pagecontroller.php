<?php

class PageController
{
	
	public $uri = array();
	public $params = array();
	
	// Requests
	public function before () { }
	public function req_get () { }
	public function req_head () { return $this->req_get(); }
	public function req_post () { return $this->req_get(); }
	public function after () { }
	
	// Variables
	public $var = array();
	public function set ($key, $value)
	{
		$this->var[$key] = $value;
	}
	public function get ($key)
	{
		return $this->var[$key];
	}
	
	// Set page alert
	public $alert_data = array();
	public function alert ($str, $type = 'info')
	{
		$this->alert_data = array('message' => $str, 'type' => $type);
	}
	
}