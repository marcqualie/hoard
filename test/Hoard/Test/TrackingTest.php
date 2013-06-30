<?php

namespace Hoard\Test;
use Model\Bucket;

class TrackingTest extends TestCase
{


    /**
     * Tracking must work directly on the bucket ID (unqique, none changable)
     */
    public function testBucketIdPayload()
    {

        // Create Bucket
        $bucket = Bucket::create(array(
            '_id' => '51d077a88dff0',
            'alias' => array(
                'test-bucket'
            )
        ));

        // Create payload
        $payload = array(
            'v' => 1,
            'b' => $bucket->id,
            'e' => 'test-event',
            'd' => array(
                'test1' => 1,
                'test2' => 2
            )
        );

        // Make request
        $response = $this->makeRequest('GET', '/api/track?test=1&payload=' . urlencode(json_encode($payload)));
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data['data']);

    }


    /**
     * Since buckets can be aliased, we must test this
     */
    public function testBucketAliasPayload()
    {

        // Create Bucket
        $bucket = Bucket::create(array(
            '_id' => '51d077a88dff0',
            'alias' => array(
                'test-bucket-2'
            )
        ));

        // Create payload
        $payload = array(
            'v' => 1,
            'b' => $bucket->alias[0],
            'e' => 'test-event',
            'd' => array(
                'test1' => 1,
                'test2' => 2
            )
        );

        // Make request
        $response = $this->makeRequest('GET', '/api/track?test=1&payload=' . urlencode(json_encode($payload)));
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data['data']);

        // Update alias and do it again
        $bucket->alias = array('test-bucket-3');
        $bucket->save();
        $response = $this->makeRequest('GET', '/api/track?test=1&payload=' . urlencode(json_encode($payload)));
        $data = json_decode($response->getContent(), true);
        $this->assertEquals($data['error']['code'], 500);
        $this->assertEquals($data['error']['message'], 'Invalid Bucket Name');

        // Musical bucket alias's!
        $bucket->alias = array('test-bucket-2');
        $bucket->save();
        $response = $this->makeRequest('GET', '/api/track?test=1&payload=' . urlencode(json_encode($payload)));
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data['data']);

    }

}
