<?php

namespace Controller;

class Stats extends Base\Page
{

	public $periods = array(
		'second'	=> array(1, 'i', 30, 'ymdHis'),
		'minute'	=> array(60, 'H:i', 30, 'ymdHi'),
		'hour'		=> array(3600, 'H', 6, 'ymdH'),
		'day'		=> array(86400, 'm/d', 7, 'ymd')
	);
	public $period = 'minute';

	public function req_get ()
	{

		$bucket = isset($_GET['bucket']) ? $_GET['bucket'] : false;
		if (empty($bucket))
		{
			exit('500 No Bucket ID Specified');
		}
		$collection = $this->app->mongo->selectCollection('event_' . $bucket);
		$stats['all'] = array();

		// Vars
		if (isset($_GET['period']) && array_key_exists($_GET['period'], $this->periods))
		{
			$this->period = $_GET['period'];
		}
		$interval = $this->periods[$this->period][0];
		$time = time();
		$now = $time - ($time % $interval) + $interval;

		// Build Data Array
		$inc = 0;
		$max = $this->periods[$this->period][2] ? $this->periods[$this->period][2] : 30;
		$decr = 1;
		$columns = array();
		foreach ($stats as $key => $data)
		{
			$inc++;
			for ($i = $max; $i >= 0; $i -= $decr)
			{
				$start = $now - ($interval * ($i + $decr));
				$end = $now - ($interval * $i);

				$columns[] = date($this->periods[$this->period][1], $start);
				$cache_key = 'data-' . $this->period . '-' . $key . '-' . $i . '-' . $start . '-' . $end;
				$count = false;
				if ($count === false)
				{
					$array = array_merge(
						array(
							't' => array(
								'$gt' => new \MongoDate($start),
								'$lte' => new \MongoDate($end)
							)
						),
						$data
					);
//					print_r($array);
//					exit;
					$count = $collection->find($array, array('_id' => 0))->count();
					echo $count;
					// set cache
				}

				$e = 'e' . date($this->periods[$this->period][3], $start);
//				$count = (int) Cache::instance()->get($e);
				$csv[$key][] = $count;
			}
		}

		// Output
		header('Content-type: text/plain');
		foreach ($columns as $column)
		{
			echo ',' . $column;
		}
		foreach ($csv as $key => $data)
		{
			echo "\n";
			echo $key;
			foreach ($data as $count)
			{
				echo ',' . $count;
			}
		}
		exit;

	}

}
