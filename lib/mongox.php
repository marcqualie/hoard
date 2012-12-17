<?php

class MongoX
{
	
	public static $conn = null;
	public static $db = null;
	public static $db_name = '';
	public static $uri = '';
	public static $connected = false;
	
	/**
	 * Connect
	 */
	public static function init ($uri, array $opt = array())
	{
		self::$uri = $uri;
		$explode = explode('/', $uri);
		self::$db_name = end($explode);
		if ($opt['connect'])
		{
			self::connect();
		}
	}
	public static function connect ()
	{
		try
		{
			self::$conn = new Mongo(self::$uri);
			self::$db = self::$conn->selectDb(self::$db_name);
			self::$connected = true;
		}
		catch (Exception $e)
		{
			//echo 'There was a problem connecting to MongoDB [' . $e->getMessage() . ']';
			//exit;
		}
	}
	
	public static function selectDb ($db_name)
	{
		return self::$conn->selectDb($db_name);
	}
	
	public static function selectCollection ($collection_name)
	{
		if ( ! self::$connected)
		{
			self::connect();
		}
		if ($collection_name)
		{
			return self::$db->selectCollection($collection_name);
		}
		else
		{
			return new MongoX_EmptyCollection;
		}
	}
	
	/**
	 * Commands
	 */
	public static function command ($cmd)
	{
		return self::$db->command($cmd);
	}
	
}

/**
 * Empty MongoCollection for silent collection exception handling
 */
class MongoX_EmptyCollection
{
	
	public function find ()
	{
		return $this;
	}
	public function sort ()
	{
		return $this;
	}
	
}