<?php

/**
 * Redirect Helper
 */
function redirect ($url)
{
	$goto = $url;
	if (strpos($goto, 'http') !== 0)
	{
		$goto = 'http://' . $_SERVER['HTTP_HOST'] . $goto;
	}
	header('Location: ' . $goto);
	exit;
}

/**
 * Array Sort
 */
function array_sort_func ($a, $b = null)
{
	static $keys;
	if ($b === null) return $keys = $a;
	foreach ($keys as $k)
	{
		if ($k[0] === '!')
		{
			$k = substr($k, 1);
			if ($a[$k] !== $b[$k])
			{
				return is_numeric($a[$k])
					? $b[$k] - $a[$k]
					: strcasecmp($b[$k], $a[$k]);
			}
		}
		else if ($a[$k] !== $b[$k])
		{
			return is_numeric($a[$k])
				? $a[$k] - $b[$k]
				: strcasecmp($a[$k], $b[$k]);
		}
	}
	return 0;
}
function array_sort (&$array)
{
	$keys = func_get_args();
	if ( ! $array)
	{
		return $keys;
	}
	array_shift($keys);
	array_sort_func($keys);
	usort($array, 'array_sort_func');
}

/**
 * Convert
 */
function normalize_bytes ($bytes, $precision = 2, $html = false)
{
	$format = function ($metric, $html) {
		if ( ! $html)
		{
			return $metric;
		}
		return ' <span class="text-muted">' . $metric . '</span>';
	};
	if ($bytes < 1024 * 1024)
	{
		return number_format($bytes / 1024, $precision) . $format('kb', $html);
	}
	if ($bytes < 1024 * 1024 * 1024)
	{
		return number_format($bytes / (1024 * 1024), $precision) . $format('mb', $html);
	}
	if ($bytes < 1024 * 1024 * 1024 * 1024)
	{
		return number_format($bytes / ( 1024 * 1024 * 1024), $precision) . $format('gb', $html);
	}
	return $bytes;
}
