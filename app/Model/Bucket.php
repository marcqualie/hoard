<?php

namespace Model;

class Bucket extends Base
{

    public static $collection = 'app';

    public $legacy = false;


    /**
     * Initialize
     */
    public function init()
    {
        if (is_object($this->id) && get_class($this->id) === 'MongoId') {
            $this->legacy = true;
        }
    }


    /**
     * Bucket Schema
     */
    public function getSchema()
    {
        return array(
            '_id' => 'String',
            'name' => 'String',
            'description' => 'String',
            'roles' => 'Hash',
            'created' => 'MongoDate',
            'updated' => 'MongoDate'
        );
    }

}
