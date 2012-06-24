<?php

/*

// construct map and reduce functions
$map = new MongoCode("function() { emit(this.user_id,1); }");
$reduce = new MongoCode("function(k, vals) { ".
    "var sum = 0;".
    "for (var i in vals) {".
        "sum += vals[i];". 
    "}".
    "return sum; }");

$sales = $db->command(array(
    "mapreduce" => "events", 
    "map" => $map,
    "reduce" => $reduce,
    "query" => array("type" => "sale"),
    "out" => array("merge" => "eventCounts")));

$users = $db->selectCollection($sales['result'])->find();

foreach ($users as $user) {
    echo "{$user['_id']} had {$user['value']} sale(s).\n";
}

*/

class MapreduceController extends PageController
{
	
	public function req_post ()
	{
		
		$this->params['query'] = $_POST['query'];
		$this->params['map-func'] = $_POST['map-func'];
		$this->params['reduce-func'] = $_POST['reduce-func'];
		$this->params['sort'] = $_POST['sort'];
		
		// Validate Query
		$query = json_decode($_POST['query'], true);
		if ($this->params['query'] && !$query)
		{
			return $this->alert('That query is invalid', 'danger');
		}
		
		// Validate Sort
		$sort = json_decode($_POST['sort'], $this->params['sort']);
		if ($this->params['sort'] && !$sort)
		{
			return $this->alert('Invalid Sort JSON');
		}
		if (!$sort)
		{
			$sort = array();
		}
		
		// Validate Map
		$map = new MongoCode($this->params['map-func']);
		if (!$map)
		{
			return $this->alert('Map function is not valid', 'danger');
		}
				
		// Validate Reduce
		$reduce = new MongoCode($this->params['reduce-func']);
		if (!$reduce)
		{
			return $this->alert('Reduce function is not valid', 'danger');
		}
		
		// Run Command
		$response = MongoX::command(array(
			"mapreduce" => "event", 
			"map" => $map,
			"reduce" => $reduce,
			"query" => $query,
			"out" => array("replace" => "tmp_mapreduce_hoardui")
		));
		$this->alert(print_r($response, true));
		
		// Query over results
		if ($response['result'])
		{
			$collection = MongoX::selectCollection($response['result']);
			$results = array();
			$cursor = $collection->find()->sort($sort)->limit(100);
			foreach ($cursor as $row)
			{
				$results[] = $row;
			}
			$this->set('output', $results);
		}
		else
		{
			$this->alert($response['errmsg'] . ' [' . $response['assertion'] . ']', 'danger');
		}
		
	}
	
	public function req_get ()
	{
		
	}
	
	public function after ()
	{
		// Set default values
		if (!$this->params['query'])
		{
			$this->params['query'] = '{ "event": " .. " }';
		}
		if (!$this->params['sort'])
		{
			$this->params['sort'] = '{ "value": -1 }';
		}
		if (!$this->params['map-func'])
		{
			$this->params['map-func'] = "function () {\n  \n}";
		}
		if (!$this->params['reduce-func'])
		{
			$this->params['reduce-func'] = "function (key, obj) {\n  \n}";
		}
		
	}
	
}