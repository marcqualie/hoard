<?php

namespace Controller;

class Track extends Base\Page
{

    public $formats = array('json');

    public function req_get ()
    {

        header('Content-Type: text/plain');

        $req = $this->app->request;
        $params = array_merge($req->query->all(), $req->request->all());
        $format = 'json';
        $dataload = $req->get('data') ? json_decode(urldecode($req->get('data')), true) : array();
        $payload = $req->get('payload') ? json_decode(urldecode($req->get('payload')), true) : array();
        $data = array_merge($dataload, $payload);

        // Event is required
        $event = $req->get('event') ?: (
            isset($data['event'])
                ? $data['event']
                : (
                    // Legacy, will be removed in next version
                    (! empty($this->uri[1]))
                        ? $this->uri[1]
                        : false
                )
            );
        if ( ! $event)
        {
            echo '500 No Event Specified';
            exit;
        }

        // Special data types
        if ($req->get('bucket'))
        {
            $bucket = $req->get('bucket');
        }
        else
        {
            $bucket = array_key_exists('appkey', $params)
                ? $params['appkey'] : (
                    array_key_exists('appkey', $data)
                        ? $data['appkey']
                        : false
                );
        }
        $bucket = trim($bucket);

        // BucketID is required
        if ( ! $bucket)
        {
            echo '500 No Bucket ID Specified';
            exit;
        }
        $bucket_exists = $this->app->mongo->selectCollection('app')->find(array('appkey' => $bucket))->count() > 0 ? true : false;
        if ($bucket_exists === false)
        {
            echo '500 Invalid Bucket ID';
            exit;
        }

        // Normalize Data
        unset($data['event']);
        unset($data['appkey']);
        unset($data['bucket']);
        unset($data['sig'], $data['hash']);

        // Append to data
        $insert = array();
        $insert['t'] = new \MongoDate();
        $insert['e'] = $event;
        $insert['d'] = $data;

        // Save Data to log
        try
        {

            $collection = $this->app->mongo->selectCollection('event_' . $bucket);
            $collection->insert($insert);
            echo $insert['_id'];
            exit;

        }

        // Could not connect (this is where queue will be needed)
        catch (MongoConnectionException $e)
        {
            echo '503 Database Exception';
            exit;
        }
        exit;

    }

}
