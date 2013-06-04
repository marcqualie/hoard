<?php

namespace Controller;

class Stats extends Base\Page {

    public $time_start = 0;
    public $time_end = 0;
    public $time_step = 0;

    public function req_get ()
    {

        // Get bucket
        $bucket = $this->app->request->get('bucket');
        if ( ! $bucket)
        {
            return $this->jsonError(500, 'No bucket specified');
        }
        $this->collection = $this->app->mongo->selectCollection('event_' . $bucket);

        // Verify input (TODO: Stronger validation and conversion)
        $period = $this->app->request->get('period') ?: false;
        $now = time();
        $default_time_gap = 1800;
        $default_time_step = 60;

        // Per day
        if ($period === 'day')
        {
            $default_time_gap = 86400;
            $default_time_step = 3600;
        }
        elseif ($period === 'hour' || $period == '3600')
        {
            $default_time_gap = 3600;
            $default_time_step = 60;
        }
        elseif ($period === 'minute' || $period == '60')
        {
            $default_time_gap = 60;
            $default_time_step = 1;
        }
        elseif ((int) $period)
        {
            $default_time_gap = $period;
            $default_time_step = $default_time_gap / 30;
        }

        // Build time groups
        $this->time_step = (int) $this->app->request->get('step') ?: $default_time_step;
        $this->time_start = ($now - $default_time_gap) - (($now - $default_time_gap) % $this->time_step);
        $this->time_end = $now - ($now % $this->time_step);


        // Detect query type
        $data = $this->action_sum();
        return $this->json($data);

    }

    public function action_sum ()
    {

        $results = array();

        // Loop over times
        for ($time = $this->time_start; $time < $this->time_end; $time += $this->time_step)
        {
            $op = array(
                array(
                    '$match' => array(
                        't' => array(
                            '$gte' => new \MongoDate($time),
                            '$lte' => new \MongoDate($time + $this->time_step)
                        )
                    ),
                ),
                array(
                    '$group' => array(
                        '_id' => '$e',
                        'c' => array(
                            '$sum' => 1
                        )
                    )
                )
            );

            $aggregate = $this->app->mongo->command(array(
                'aggregate' => $this->collection->getName(),
                'pipeline' => $op
            ));
            $result_arr = array('all' => 0);
            foreach ($aggregate['result'] as $result)
            {
                $result_arr['all'] += $result['c'];
                $result_arr[$result['_id']] = $result['c'];
            }
            $results[] = array(
                'range' => array($time, $time + $this->time_step),
                'events' => $result_arr
            );

        }

        // Output Results
        return $this->json($results, 200, array(
            'range' => array($this->time_start, $this->time_end),
            'step' => $this->time_step
        ));

    }

}
