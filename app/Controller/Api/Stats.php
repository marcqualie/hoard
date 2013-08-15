<?php

namespace Controller\Api;
use Model\Bucket;

class Stats extends \Controller\Base\Api
{
    private $time_start = 0;
    private $time_end = 0;
    private $time_step = 0;
    private $time_gap;

    private $collection;
    private $bucket_id;
    private $bucket;
    private $event;
    private $query = array();

    public function exec ()
    {

        // Get bucket
        $this->bucket_id = $this->app->request->get('bucket');
        if (! $this->bucket_id) {
            return $this->error('No bucket specified');
        }
        $this->bucket = Bucket::findById($this->bucket_id);
        if (! $this->bucket) {
            return $this->error('Invalid Bucket ID', 404);
        }
        $this->collection = $this->app->mongo->selectCollection($this->bucket->event_collection);

        // Event filtering
        $this->event = $this->app->request->get('event');

        // Custom Queries
        $query = $this->app->request->get('query');
        if (is_string($query)) {
            $this->query = json_decode($query, true) ?: null;
        }

        // Verify input (TODO: Stronger validation and conversion)
        $period = $this->app->request->get('period') ?: false;
        $now = time();
        $default_time_gap = 1800;
        $default_time_step = 60;

        // Per day
        if ($period === 'month' || $period === 18144000) {
            $default_time_gap = 18144000;
            $default_time_step = 86400;
        } elseif ($period === 'week' || $period === 604800) {
            $default_time_gap = 604800;
            $default_time_step = 86400;
        } elseif ($period === 'day' || $period === '86400') {
            $default_time_gap = 86400;
            $default_time_step = 3600;
        } elseif ($period === 'hour' || $period == '3600') {
            $default_time_gap = 3600;
            $default_time_step = 60;
        } elseif ($period === 'minute' || $period == '60') {
            $default_time_gap = 60;
            $default_time_step = 1;
        } elseif ((int) $period) {
            $default_time_gap = $period;
            $default_time_step = $default_time_gap / 30;
        }

        // Build time groups
        $this->time_step = (int) $this->app->request->get('step') ?: $default_time_step;
        $this->time_gap = $default_time_gap;

        // Make sure there aren't too many steps (possiblity of crashing stats engine)
        if ($this->time_gap / $this->time_step > 300) {
            $this->time_step = $this->time_gap / 300;
        }

        // Create time ranges
        $this->time_start = ($now - $this->time_gap) - (($now - $this->time_gap) % $this->time_step);
        $this->time_end = $now - ($now % $this->time_step) + $this->time_step;

        // Build Query
        $results = array();
        $query = array();
        if ($this->event) {
            $query['e'] = $this->event;
        }
        if ($this->query) {
            foreach ($this->query as $key => $val) {
                $query['d.' . $key] = $val;
            }
        }

        // Function
        $func = '$sum';
        $func_inc = 1;
        $func_override = $this->app->request->get('func');
        if ($func_override) {
            list ($func_name, $func_var) = explode(':', $func_override);
            if ($func_name === 'avg') {
                $func = '$avg';
                $func_inc = '$d.' . $func_var;
            }
        }

        // Loop over times
        for ($time = $this->time_start; $time < $this->time_end; $time += $this->time_step) {
            $query['t'] = array(
                '$gte' => new \MongoDate($time),
                '$lte' => new \MongoDate($time + $this->time_step)
            );
            $op = array(
                array(
                    '$match' => $query,
                ),
                array(
                    '$group' => array(
                        '_id' => '$e',
                        'v' => array(
                            $func => $func_inc
                        )
                    )
                )
            );

            $aggregate = $this->app->mongo->command(array(
                'aggregate' => $this->collection->getName(),
                'pipeline' => $op
            ));
            $count = 0;
            $result_arr = array();
            foreach ($aggregate['result'] as $result) {
                $count += $result['v'];
                $result_arr[$result['_id']] = $result['v'];
            }

            // Calculate Average
            if ($func === '$avg') {
                if ($result_arr) {
                    $count = round($count / count($result_arr), 2);
                }
            }

            $results[] = array(
                'range' => array(
                    $time,
                    $time + $this->time_step
                ),
                'count' => $count,
                'events' => $result_arr
            );

        }

        // Output Results
        return array(
            'data' => $results,
            'meta' => array(
                'range' => array(
                    $this->time_start,
                    $this->time_end
                ),
                'step' => $this->time_step
            )
        );

    }

}
