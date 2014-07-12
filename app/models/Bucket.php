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
            ],
            'sort' => [
                'created_at' => -1
            ]
        ]);
    }

    public function getAverage()
    {
        $periods = func_get_args();
        $trend = [];
        foreach ($periods as $period) {
            $since = new MongoDate(time() - $period);
            $count = count(Event::find([
                [
                    'bucket_id' => $this->getId(),
                    'created_at' => [
                        '$gt' => $since
                    ]
                ]
            ]));
            $trend[] = round($count / $period * 60, 2);
        }
        return $trend;
    }

    public function getTrend()
    {
        $averages = $this->getAverage(300, 3600);
        return ($averages[0] - $averages[1]) * 10;
    }

}
