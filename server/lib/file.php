<?php

class File
{
	
	public $name			= '';
	public $location		= '';
	public $exists			= null;
	
	public function __construct ($name)
	{
		$this->name = $name;
		$this->location = $name;
	}
	
	public function read ()
	{
		if (!$this->exists()) return false;
		return file_get_contents($location);
	}
	
	public function write ($data)
	{
		if (!$this->exists()) return false;
		file_put_contents($this->location);
		return true;
	}
	
	public function exists ()
	{
		if ($this->exists !== null) return $this->exists;
		$exists = file_exists($this->location);
		$this->exists = $exists ? true : false;
		return $this->exists;
	}
	
}