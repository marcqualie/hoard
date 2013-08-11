<?php

namespace Hoard\Test\Bucket;
use Hoard\Test\TestCase;
use Model\Bucket;

class CreateTest extends TestCase
{

    public function testCreateBucket()
    {

        $bucket_data = array(
            '_id' => 'test-bucket',
            'name' => 'Test Bucket'
        );
        $bucket = new Bucket($bucket_data);
        $this->assertFalse(Bucket::exists($bucket->id));
        $bucket->save();
        $this->assertTrue(Bucket::exists($bucket->id));

    }

}
