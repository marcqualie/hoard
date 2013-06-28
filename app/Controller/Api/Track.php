<?php

namespace Controller\Api;
use Model\Bucket;
use Hoard\Payload;
use Exception;

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

        $debug = $this->app->request->get('debug') ? true : false;

        // Get JSON Body
        if ($this->app->request->getMethod() === 'POST') {
            $postBody = file_get_contents('php://input');
            if (! $postBody) {
                return $this->error('No data', 500);
            }
            $payload_data = json_decode($postBody, true);
        } else {
            $payload_data = json_decode($this->app->request->get('payload') ?: '', true);
        }

        // Convert into standardized payload
        $payload = new Payload($payload_data);
        if (! $payload->isVersionSupported()) {
            return $this->error('Payload version ' . $payload->version . ' not supported');
        }

        // Verify Bucket Credentials
        if (! $payload->bucket) {
            return $this->error('No Bucket name specified', 500);
        }
        $bucket_id = $payload->bucket;
        $bucket = Bucket::findById($bucket_id);
        if ($bucket === null) {
            return $this->error('Invalid Bucket Name');
        }

        // Verify Event Credentials
        if (! $payload->event) {
            return $this->error('No event specified');
        }

        // Save Event
        $insert = array();
        $insert['t'] = $payload->time;
        $insert['e'] = $payload->event;
        $insert['d'] = $payload->data;

        // Save Data to log
        $id = null;
        try {
            $collection = $this->app->mongo->selectCollection($bucket->event_collection);
            $collection->insert($insert);
            $id = (String) $insert['_id'];
        }

        // Could not connect (this is where queue will be needed)
        catch (\Exception $e) {
            // TODO: Queue event for later processing
            return $this->error('Database Exception', 503);
        }

        // Output Results
        $output = array(
            'data' => array(
                'id' => $id
            )
        );
        if ($debug) {
            $output['debug'] = array(
                'payload' => $payload->asArray()
            );
        }
        return $output;

    }

}
