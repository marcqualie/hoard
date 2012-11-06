<?php

/**
 * Store raw data for later querying
 *
 * Usage:
 *	
 *		$event = Hoard::track('php_syntax_error', array(
 *			'uid'		=> 123456,
 *			'file'		=> '/path/to/root/index.php'
 *			'line'		=> 10,
 *			'uri'		=> 'http://www.marcqualie.com/'
 *			'message'	=> 'You have an error in your PHP Syntax',
 *			'trace'		=> array()
 *		));
 *
 * @version 0.0.3
 * @author Marc Qualie <marc@marcqualie.com>
 */

class Hoard
{
	
	/* Application Settings */
	public static $appkey			= '';
	public static $secret			= '';
	
	/* Remote server settings */
	public static $server			= '';
	public static $version			= '0.0.3';
	public static $initialized		= false;

	public static $error			= '';
		
	/**
	 * Initialize Hoard Config
	 *
	 * @param config		Array			Contains connection info and application keys
	 * @return 				Boolean			True on success, false if config is invalid
	 */
	public static function init ($config)
	{
		foreach ($config as $k => $v)
		{
			self::$$k = $v;
		}
		if (self::$server && self::$appkey && self::$secret)
		{
			self::$initialized = true;
			return true;
		}
		self::$error = 'Invalid Configuration';
		return false;
	}
	
	/**
	 * Track events
	 *
	 * @param file			String			Location of the file which triggered the event on the server
	 * @param line			Number			Line number where the error can be isolated
	 * @param location		String			Url where you can navigate to replicate the problem
	 */
	public static function track ($event, array $data)
	{
		
		// ONly send data if you have a valid secret
		if (self::$initialized === false)
		{
			self::$error = 'Not initialized';
			return false;
		}
		if (self::$server === '')
		{
			self::$error = 'No server defined';
			return false;
		}
		if (self::$appkey === '')
		{
			self::$error = 'No $APPKEY';
			return false;
		}
		
		// Auto generate some data from the environment
		$data['event']				= $event;
		$data['host']				= $_SERVER['HTTP_HOST'];
		$data['server']				= array('name' => $_SERVER['SERVER_NAME'], 'ipv4' => $_SERVER['SERVER_ADDR']);
		$data['appkey']				= self::$appkey;
		
		// TODO: Parse / Verify Data
		if (array_key_exists('file', $data))
		{
			$data['file'] = str_replace(DOCROOT, '', $data['file']);
		}
		
		// Track sessions for tracking breadcrumbs style events
		$sess = session_id();
		if ($sess)
		{
			$data['sess'] = $sess;
		}
		
		// Generate Signature and encode
		$data['hash'] = md5(uniqid());
		$data['sig'] = sha1(self::$secret . $data['hash']);
		
		// Check for special params
		$async = true;
		if (array_key_exists('$async', $data) && $data['$async'] === false)
		{
			$async = false;
			unset($data['$async']);
		}
		
		// Format data into post string
		$postfields = array(
			'format' => 'json',
			'data' => json_encode($data)
		);
		$post_params = array();
		foreach ($postfields as $key => $val)
		{
			$post_params[] = $key . '=' . urlencode($val);
		}
		$post_string = implode('&', $post_params);
		
		// Send Data
		$parts = parse_url(self::$server . '/track/' . $event);
		$fp = fsockopen($parts['host'], array_key_exists('port', $parts) ? $parts['port'] : 80, $errno, $errstr, 30);
		$response = '';
		if ($fp !== 0)
		{
			$out  = "POST " . $parts['path'] . " HTTP/1.1\r\n";
			$out .= "Host: " . $parts['host'] . "\r\n";
			$out .= "User-Agent: PHP " . self::$version . "\r\n";
			$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$out .= "Content-Length: " . strlen($post_string) . "\r\n";
			$out .= "Connection: Close\r\n\r\n";
			$out .= $post_string;
			fwrite($fp, $out);
			if ($async === false)
			{
				while (!feof($fp))
				{
					$response .= fgets($fp, 128);
				}
			}
			fclose($fp);
		}
		
		// Output
		return $async ? true : $response;
		
	}
	
	/**
	 * Find data based on input params
	 *
	 * @return		Array				Data matching your query. Blank array if no data
	 */
	public static function find ()
	{
		return array();
	}
	
	/**
	 * Stats
	 *
	 * TODO: Need to create stats to display info such as usage information and any hoard related errors
	 */
	public static function stats ()
	{
		
	}

	/**
	 * Application error reporting
	 */
	public static function last_error ()
	{
		return self::$error;
	}
	
}