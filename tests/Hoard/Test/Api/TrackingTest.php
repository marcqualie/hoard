<?php

namespace Hoard\Test\Api;
use Hoard\Test\TestCase;
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
        $response = $this->makeApiRequest('GET', '/api/track?payload=' . urlencode(json_encode($payload)));
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
        $response = $this->makeApiRequest('GET', '/api/track?payload=' . urlencode(json_encode($payload)));
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data['data']);

        // Update alias and do it again
        $bucket->alias = array('test-bucket-3');
        $bucket->save();
        $response = $this->makeApiRequest('GET', '/api/track?payload=' . urlencode(json_encode($payload)));
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(404, $data['error']['code']);
        $this->assertEquals('Invalid Bucket', $data['error']['message']);

        // Musical bucket alias's!
        $bucket->alias = array('test-bucket-2');
        $bucket->save();
        $response = $this->makeApiRequest('GET', '/api/track?payload=' . urlencode(json_encode($payload)));
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data['data']);

    }


    /**
     * Make sure non-existing buckets are caught
     */
    public function testNonExistingBucketPayload()
    {

        // Create payload
        $payload = array(
            'v' => 1,
            'b' => 'non-existing-bucket',
            'e' => 'test-event',
            'd' => array()
        );

        // Make request
        $response = $this->makeApiRequest('GET', '/api/track?payload=' . urlencode(json_encode($payload)));
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(404, $data['error']['code']);
        $this->assertEquals('Invalid Bucket', $data['error']['message']);

    }


    /**
     * A Bucket param must be passed as part of the payload
     */
    public function testNoBucketPayload()
    {

        // Create payload
        $payload = array(
            'v' => 1,
            'e' => 'test-event',
            'd' => array()
        );

        // Make request
        $response = $this->makeApiRequest('GET', '/api/track?payload=' . urlencode(json_encode($payload)));
        $data = json_decode($response->getContent(), true);
        $this->assertEquals($data['error']['code'], 400);
        $this->assertEquals($data['error']['message'], 'No Bucket specified');

    }


    /**
     * An Event param must be passed as part of the payload
     */
    public function testNoEventPayload()
    {

        // Create payload
        $payload = array(
            'v' => 1,
            'b' => 'test-bucket',
            'd' => array()
        );

        // Make request
        $response = $this->makeApiRequest('GET', '/api/track?payload=' . urlencode(json_encode($payload)));
        $data = json_decode($response->getContent(), true);
        $this->assertEquals($data['error']['code'], 400);
        $this->assertEquals($data['error']['message'], 'No Event specified');

    }

}
