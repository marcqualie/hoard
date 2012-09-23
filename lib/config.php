<?php

class Config {
	
	private static $data = array();

	/**
	 * Load configuration file
	 */
	public static function load ($name = 'default')
	{
		$file = APPROOT . '/config/' . $name . '.php';
		if (file_exists($file))
		{
			self::$data = include $file;
		}
		return self::$data;
	}

	/**
	 * Read config data
	 */
	public static function  get ($key)
	{
		return self::$data[$key];
	}

}