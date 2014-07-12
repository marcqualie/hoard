<?php

class Event extends Phalcon\Mvc\Collection
{

    // Attributes
    public $bucket_id;
    public $name;
    public $data;
    public $created_at;

    public function getSource()
    {
        return 'events';
    }

    public function beforeCreate()
    {
        if (! $this->created_at) {
            $this->created_at = new MongoDate();
        }
    }

    public function getBucket()
    {
        return Bucket::findById($this->bucket_id);
    }

}
