<?php

class User extends Phalcon\Mvc\Collection
{

    // Attributes
    public $name;
    public $username;
    public $email;
    public $password;
    public $created_at;
    public $updated_at;

    public function getSource()
    {
        return 'users';
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

    public function getBuckets()
    {
        return Bucket::find([
            [
                'roles.' . $this->getId() => [
                    '$exists' => 1
                ]
            ]
        ]);
    }

}
