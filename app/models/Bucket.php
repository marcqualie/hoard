<?php

class Bucket extends Phalcon\Mvc\Collection
{

    // Attributes
    public $name;
    public $roles;
    public $description;
    public $created_at;
    public $updated_at;

    public function getSource()
    {
        return 'buckets';
    }

    public function beforeCreate()
    {
        if (! $this->created_at) {
            $this->created_at = new MongoDate();
        }
    }

    public function beforeSave()
    {
        $this->updated_at = new MongoDate();
    }

    public function getEvents()
    {
        return Event::find([
            [
                'bucket_id' => $this->getId()
            ]
        ]);
    }

}
