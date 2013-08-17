<?php

namespace Hoard\Test\Model;
use Hoard\Test\TestCase;
use Model\Base;

class BaseTest extends TestCase
{


    /**
     * The default model should set and data passed to it
     */
    public function testConstuctSetsData()
    {
        $data = array(
            '_id' => 1,
            'testingData' => uniqid()
        );
        Base::$collection = 'model_test';
        $model = new Base($data);
        $this->assertEquals($data['_id'], $model->id);
        $this->assertEquals($data['testingData'], $model->testingData);
    }


    /**
     * Saving data must store it in the database
     */
    public function testModelSave()
    {
        $data = array(
            '_id' => 1,
            'testingData' => uniqid()
        );
        Base::$collection = 'model_test';
        $this->assertEquals(0, $this->mongo->selectCollection(Base::$collection)->count());
        $model = new Base($data);
        $this->assertEquals(0, $this->mongo->selectCollection(Base::$collection)->count());
        $model->save();
        $this->assertEquals(1, $this->mongo->selectCollection(Base::$collection)->count());
        $this->assertEquals($model->asArray(), $this->mongo->selectCollection(Base::$collection)->findOne());
    }

}
