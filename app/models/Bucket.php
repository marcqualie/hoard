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

    public function getEventCollection()
    {
        $model = new Event;
        $collection = $model->getConnection()->selectCollection('events_' . $this->getId());
        return $collection;
    }

    public function getEvents()
    {
        $events = [];
        $cursor = $this->getEventCollection()->find()->sort(['created_at' => -1]);
        foreach ($cursor as $document) {
            $object = new stdClass;
            foreach ($document as $key => $value) {
                $object->$key = $value;
            }
            $events[] = $object;
        }
        return $events;
    }

     public function getEventCount()
    {
        return $this->getEventCollection()->count();
    }

    public function getAverage()
    {
        $periods = func_get_args();
        $trend = [];
        foreach ($periods as $period) {
            $since = new MongoDate(time() - $period);
            $count = $this->getEventCollection()->find(['created_at' => ['$gt' => $since]])->count();
            $trend[] = round($count / $period, 2);
        }
        return $trend;
    }

    public function getTrend()
    {
        $averages = $this->getAverage(60);
        return $averages[0];
    }

    public function getStorageUsage()
    {
        $stats = $this->getConnection()->command(['collStats' => $this->getEventCollection()->getName()]);
        return round($stats['storageSize'] / 1024 / 1024, 2);
    }

}
