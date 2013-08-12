<?php

namespace Hoard\Test\Bucket;
use Hoard\Test\TestCase;
use Model\Bucket;

class CreateTest extends TestCase
{


    /**
     * Test that the model abstraction is working
     */
    public function testCreateBucket()
    {

        $bucket_data = array(
            '_id' => 'test-bucket',
            'description' => 'Test Bucket',
        );
        $bucket = new Bucket($bucket_data);
        $this->assertFalse(Bucket::exists($bucket->id));
        $bucket->save();
        $this->assertTrue(Bucket::exists($bucket->id));

    }


    /**
     * Make sure the data is actually stored in the databse
     */
    public function testCreateBucketDataStored()
    {
        $bucket_data = array(
            '_id' => 'test-bucket',
            'description' => 'Test Bucket',
        );
        $collection = $this->mongo->selectCollection(Bucket::$collection);
        $this->assertEquals(0, $collection->count());
        $bucket = new Bucket($bucket_data);
        $bucket->save();
        $this->assertEquals(1, $collection->count());
        $this->assertEquals(
            array('description' => 'Test Bucket'),
            $collection->findOne(array(), array('_id' => 0, 'description' => 1))
        );
    }

}
