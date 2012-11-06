<?php

class Cache {
	
	/**
	 * Singelton Access
	 */
	public static $instances = array();
	public static function instance ($ns = 'default')
	{
		if ( ! array_key_exists($ns, self::$instances))
		{
			$instance = new Cache($ns);
			self::$instances[$ns] = $instance;
		}
		return self::$instances[$ns];
	}

	/**
	 * Instance
	 */
	public $engine;
	public $enabled = false;
	public function __construct ()
	{
		$this->engine = new Memcache;
		$connect = $this->engine->addServer('127.0.0.1', 11211);
		if ( ! $connect)
		{
			return;
		}
		$this->enabled = true;
	}

	public function get ($key)
	{
		if ( ! $key) return false;
		if ( ! $this->enabled) return false;
		return $this->engine->get($key);
	}
	public function set ($key, $val, $expire = 3600)
	{
		if ( ! $this->enabled) return false;
		return $this->engine->set($key, $val, false, $expire);
	}

	public function increment ($key, $incr = 1, $expire)
	{
		if ( ! $this->enabled) return false;
		if ( ! $this->get($key))
		{
			$this->set($key, 5, $expire = 86400);
		}
		return $this->engine->increment($key, $incr);
	}

}