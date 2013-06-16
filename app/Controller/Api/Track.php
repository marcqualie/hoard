<?php

namespace Controller\Api;
use Model\Bucket;

class Track extends \Controller\Base\Api {

    private $time_start = 0;
    private $time_end = 0;
    private $time_step = 0;
    private $time_gap;

    private $collection;
    private $bucket;
    private $event;

    public function exec ()
    {

        // Get JSON Body
        $postBody = file_get_contents('php://input');
        if (! $postBody) {
            return $this->error('No data', 500);
        }
        $payload = json_decode($postBody, true);

        // Sort Default Meta Data
        $meta = array_merge(
            array(
                'time' => time()
            ),
            isset($payload['meta']) ? $payload['meta'] : array()
        );

        // Verify Bucket Credentials
        if (! isset($payload['bucket'])) {
            return $this->error('No Bucket name specified', 500);
        }
        $bucket_id = $payload['bucket'];
        $bucket = Bucket::findById($bucket_id);
        if ($bucket === null) {
            return $this->error('Invalid Bucket Name');
        }

        // Verify Event Credentials
        if (! $payload['event']) {
            return $this->error('No event specified');
        }

        // Run some checks on data
        if (! isset($payload['data'])) {
            $payload['data'] = array();
        }

        // Save Event
        $insert = array();
        $insert['t'] = new \MongoDate($meta['time']);
        $insert['e'] = $payload['event'];
        $insert['d'] = $payload['data'];

        // Save Data to log
        $id = null;
        try {
            $collection = $this->app->mongo->selectCollection($bucket->event_collection);
            $collection->insert($insert);
            $id = $insert['_id'];
        }

        // Could not connect (this is where queue will be needed)
        catch (\Exception $e) {
            // TODO: Queue event for later processing
            return $this->error('Database Exception', 503);
        }

        // Output Results
        return array(
            'data' => array(
                'id' => $id
            ),
            'meta' => array(
                'payload' => $payload
            )
        );

    }

}
