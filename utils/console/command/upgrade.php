<?php

/**
 * Upgrade
 */
if ($action === 'upgrade')
{

	$stats = array(
		'ok' => 0,
		'corrupt' => 0,
		'skipped' => 0
	);
	$old_collection = App::$mongo->selectCollection('event');
	$events = $old_collection->find();
	foreach ($events as $event)
	{
		if ( ! $event['appkey'])
		{
			$stats['corrupt']++;
			return;
		}
		$new_collection = App::$mongo->selectCollection('event_' . $event['appkey']);
		$found = $new_collection->findOne(array('_id' => $event['_id']));
		if ($found)
		{
			$stats['skipped']++;
		}
		else
		{
			$d = array();
			$d['_id'] = $event['_id'];
			$d['t'] = $event['date'];
			$d['d'] = $event;
			$d['e'] = $event['event'];
			unset($d['d']['_id']);
			unset($d['d']['date']);
			unset($d['d']['appkey']);
			unset($d['d']['event']);
//			print_r($d);
			$saved = $new_collection->save($d, array(
//				'safe' => true,
//				'fsync' => true
			));
//			if ($saved['ok'])
//			{
//			}
//			print_r($saved);
			$stats['ok']++;
		}
		$old_collection->remove(array('_id' => $event['_id']));
	}
	print_r($stats);

}
