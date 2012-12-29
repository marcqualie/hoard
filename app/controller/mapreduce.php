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

	public function before ()
	{

		// Set params from $_POST
		foreach ($_POST as $key => $value)
		{
			$this->params[$key] = $value;
		}

		// Set default values
		if ( ! isset($this->params['query']))
		{
			$this->params['query'] = '{ "e": "" }';
		}
		if ( ! isset($this->params['sort']))
		{
			$this->params['sort'] = '{ "value": -1 }';
		}
		if ( ! isset($this->params['map-func']))
		{
			$this->params['map-func'] = "emit(data.message, 1);";
		}
		if ( ! isset($this->params['reduce-func']))
		{
			$this->params['reduce-func'] = "var sum = 0;\nfor (var i in obj) {\n  sum += obj[i];\n}\nreturn sum;";
		}

	}
	
	public function req_post ()
	{
		
		// Validate Query
		$query = json_decode($_POST['query'], true);
		if ($this->params['query'] && !$query)
		{
			return $this->alert('That query is invalid', 'danger');
		}
		
		// Validate Sort
		$sort = json_decode($_POST['sort'], $this->params['sort']);
		if ($this->params['sort'] && ! $sort)
		{
			return $this->alert('Invalid Sort JSON');
		}
		if ( ! $sort)
		{
			$sort = array();
		}
		
		// Validate Map
		$map = new MongoCode('function () { var data = this.d; var event = this.e; ' . $this->params['map-func'] . ' }');
		if ( ! $map)
		{
			return $this->alert('Map function is not valid', 'danger');
		}
				
		// Validate Reduce
		$reduce = new MongoCode('function (key, obj) { ' . $this->params['reduce-func'] . ' }');
		if ( ! $reduce)
		{
			return $this->alert('Reduce function is not valid', 'danger');
		}

		// Get event collection from AppKey
		$appkey = isset($this->params['appkey']) ? $this->params['appkey'] : '';
//		print_r($this->params);
		if ( ! $appkey)
		{
			return $this->alert('Please select your application from the Dropdown');
		}
		
		// Run Command
		$response = MongoX::command(array(
			"mapreduce"        => 'event_' . $appkey,
			"map"              => $map,
			"reduce"           => $reduce,
			"query"            => $query,
			"out"              => array("replace" => "tmp_mapreduce_hoard")
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

			// Output RAW Data
			if (isset($_GET['format']) && $_GET['format'] === 'json')
			{

			}

			// 
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

		$this->set('title', 'Hoard - Map Reduce');
		
	}
	
}