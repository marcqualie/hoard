<?php

class Auth
{
	
	public static $cookie = 'user';
	public static $id = null;
	public static $admin = false;
	public static $user = array();
	public static $buckets = array();
	
	public static function init ()
	{
		
		self::$cookie = 'u' . crc32(self::$cookie . '.' . COOKIE_DOMAIN);

		$cookie = isset($_COOKIE[self::$cookie]) ? $_COOKIE[self::$cookie] : '';
		if ( ! $cookie)
		{
			return;
		}
		$data = self::decrypt($cookie);
		list ($id, $token) = explode(':', $data);
		if (!$id || !$token)
		{
			return;
		}
		$collection = App::$mongo->selectCollection('user');
		$user = $collection->findOne(array(
				'_id' => new MongoId($id)
			), array(
				'email' => 1,
				'token' => 1,
				'admin' => 1
			)
		);
		if (!$user)
		{
			return;
		}
		
		// Populate User Data
		self::$user = $user;
		self::$id = $id;
		self::$admin = $user['admin'] ? true : false;
		
		// Populate Apps
		$cursor = App::$mongo->selectCollection('app')->find(array(
			'$or' => array(
				array(
					'roles.' . self::$id => array('$exists' => 1)
				),
				array(
					'roles.all' => array('$exists' => 1)
				)
			)
		));
		self::$buckets = iterator_to_array($cursor);

	}
	
	/**
	 * Logging in and out
	 */
	public static function login ($email, $password)
	{
		$collection = App::$mongo->selectCollection('user');
		$user = $collection->findOne(array("email" => $email));
		if (!$user['password'])
		{
			return array('error' => 404, 'message' => 'No such user');
		}
		if ($user['password'] !== self::password($password))
		{
			return array('error' => 401, 'message' => 'Invalid Password');
		}
		self::login_apply((String) $user['_id'], $user['token']);
		return array('message' => 'Loging Success');
	}
	public static function login_apply ($uid, $token)
	{
		$data = self::encrypt($uid . ':' . $token);
		$_COOKIE[self::$cookie] = $data;
		setcookie(self::$cookie, $data, 0, '/', COOKIE_DOMAIN, COOKIE_SECURE, COOKIE_HTTP);
	}
	public static function logout ()
	{
		unset($_COOKIE[self::$cookie]);
		setcookie(self::$cookie, false, time() / 2, '/', COOKIE_DOMAIN, COOKIE_SECURE, COOKIE_HTTP);
	}
	
	/**
	 * Encryption
	 */
	public static function encrypt ($str)
	{
		$token = base64_encode($str);
		return $token;
	}
	public static function decrypt ($token)
	{
		$str = base64_decode($token);
		return $str;
	}
	
	/**
	 * Verify application keys when writing and reading data
	 */
	public static function verify_appkey ($appkey, $token, $sig)
	{
		return false;
	}
	
	/**
	 * Password generator
	 */
	public static function password ($str)
	{
		return sha1(md5(sha1(md5($str))));
	}
	
}
