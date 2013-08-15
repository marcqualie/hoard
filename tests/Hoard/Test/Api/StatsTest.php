<?php

namespace Hoard\Test\Api;
use Hoard\Test\TestCase;
use Model\Bucket;

class StatsTest extends TestCase
{


    /**
     * Make sure the format is correct for each range
     * @dataProvider provider
     */
    public function testRangeOutput($period, $step, $count)
    {

        // Create Bucket
        $bucket = $this->createTestBucket();

        // Make request
        $response = $this->makeRequest('GET', '/api/stats?period=' . $period . '&bucket=' . $bucket->id);
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('time', $data);

        // Assert hard values for this range
        $this->assertCount($count, $data['data']);
        $this->assertEquals($step, $data['meta']['step']);

        // Correct keys are required for data points
        $this->assertArrayHasKey('range', $data['data'][0]);
        $this->assertArrayHasKey('count', $data['data'][0]);
        $this->assertArrayHasKey('events', $data['data'][0]);

        // Ranges should be calculated properly
        $this->assertEquals($data['meta']['range'][0], $data['data'][0]['range'][0]);
        $this->assertEquals($data['meta']['range'][0] + $data['meta']['step'], $data['data'][0]['range'][1]);
        $this->assertEquals($data['meta']['range'][1] - $data['meta']['range'][0], count($data['data']) * $data['meta']['step']);

    }


    /**
     * Ranges
     *
     * Data is in the following format. period, step, count
     */
    public function provider()
    {
        return array(
            array('default', 60, 30),
            array('month', 86400, 30),
            array('week', 86400, 7),
            array('day', 3600, 24),
            array('hour', 60, 60),
            array('minute', 1, 60),
        );
    }
}
