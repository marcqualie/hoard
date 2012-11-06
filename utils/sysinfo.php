<?php

$json = array();

// Info
$json['node'] = 'london1';

// Usage
$top_exec = shell_exec("top -n1 -b | head -n5");
if ( ! $top_exec)
{
	exit;
}
foreach (explode("\n", $top_exec) as $l) {
	if (strpos($l, 'load average') > -1) {
		$l = str_replace(array('%', ' '), '', $l);
		preg_match('/loadaverage:([0-9\.]+),([0-9\.]+),([0-9\.]+)/', $l, $m);
		$json['cpu']['av'] = array($m[1], $m[2], $m[3]);
	}
	if (strpos($l, 'Cpu') > -1) {
		$l = str_replace(array('%', ',', ' '), '', $l);
		preg_match('/([0-9\.]+)us([0-9\.]+)sy([0-9\.]+)ni([0-9\.]+)id/', $l, $m);
		$json['cpu']['us'] = $m[1];
		$json['cpu']['sy'] = $m[2];
		$json['cpu']['ni'] = $m[3];
		$json['cpu']['id'] = $m[4];
	}
	if (strpos($l, 'Mem') > -1) {
		$l = str_replace(array('k', ',', ' '), '', $l);
		preg_match('/([0-9.]+)total([0-9.]+)used([0-9.]+)free/', $l, $m);
		$json['mem'] = array('total' => $m[1], 'free' => $m[3]);
	}
}

// Disk Space
$json['disk'] = array();
$json['disk']['total'] = round(disk_total_space('/') / 1024 / 1024);
$json['disk']['used'] = round((disk_total_space('/') - disk_free_space('/')) / 1024 / 1024);

// Cache
if (class_exists('Memcache'))
{
	$memcache = new Memcache;
	$memcache->addServer('127.0.0.1', 11211);
	$stats = $memcache->getStats();
	$json['cache'] = array();
	$json['cache']['used'] = $stats['bytes'] ? $stats['bytes'] : $stats['mem_used']; // changed for couchbase
	$json['cache']['total'] = $stats['limit_maxbytes'];
	$json['cache']['hits'] = $stats['get_hits'];
	$json['cache']['req'] = $stats['cmd_get'];
}

// Send to Hoard
$ch = curl_init('http://hoard.marcqualie.com/track/ping');
$post = array(
	'appkey' => '505e810a36fd0',
	'data' => json_encode($json)
);
curl_setopt_array($ch, array(
	CURLOPT_RETURNTRANSFER		=> true,
	CURLOPT_HEADER				=> false,
	CURLOPT_POST				=> true,
	CURLOPT_POSTFIELDS			=> $post
));
$id = curl_exec($ch);
curl_close($ch);
echo $id;

