<?php

namespace Model;

class Bucket extends Base
{

    public static $collection = 'app';
    public static $regex_id = '/^[a-z]{1}[a-z0-9\-]{4,30}[a-z0-9]{1}$/';

    public $legacy = false;
    public $event_collection;


    /**
     * Initialize
     */
    public function init()
    {
        $this->event_collection = 'event_' . ($this->appkey ?: $this->id);
        if (is_object($this->id) && get_class($this->id) === 'MongoId') {
            $this->legacy = true;
        }
        if (empty($this->data['description'])) {
            $this->data['description'] = $this->data['name'];
        }
    }


    /**
     * Bucket Schema
     */
    public function getSchema()
    {
        return array(
            '_id' => 'String',
            'description' => 'String',
            'roles' => 'Hash',
            'created' => 'MongoDate',
            'updated' => 'MongoDate'
        );
    }

}
