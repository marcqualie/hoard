<?php

namespace Hoard\Test\Api;
use Hoard\Test\TestCase;
use Model\Bucket;

class StatsTest extends TestCase
{

    /**
     * Make sure the format is correct for the hourly range
     */
    public function testHourlyRange()
    {

        // Create Bucket
        $bucket = $this->createTestBucket();

        // Make request
        $response = $this->makeRequest('GET', '/api/stats?period=hour&bucket=' . $bucket->id);
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('time', $data);
        $this->assertCount(60, $data['data']);
        $this->assertArrayHasKey('range', $data['data'][0]);
        $this->assertArrayHasKey('count', $data['data'][0]);
        $this->assertArrayHasKey('events', $data['data'][0]);
        $this->assertEquals($data['meta']['range'][0], $data['data'][0]['range'][0]);
        $this->assertEquals($data['meta']['range'][0] + $data['meta']['step'], $data['data'][0]['range'][1]);
        $this->assertEquals(60, $data['meta']['step']);
        $this->assertEquals($data['meta']['range'][1] - $data['meta']['range'][0], count($data['data']) * $data['meta']['step']);

    }

}
