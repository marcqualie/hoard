<?php

namespace Model;

class User extends Base
{

    public static $collection = 'user';


    public function getSchema()
    {
        return array(
            '_id' => 'ObjectId',
            'email' => 'Email',
            'password' => 'String',
            'token' => 'String',
            'apikeys' => 'Hash',
            'admin' => 'Boolean',
            'created' => 'MongoDate',
            'updated' => 'MongoDate'
        );
    }


    /**
     * Get buckets this user has access to
     */
    public function getBuckets()
    {
        $buckets = Bucket::find(
            array(
                '$or' => array(
                    array(
                        'roles.' . $this->id => array(
                            '$exists' => 1
                        )
                    ),
                    array(
                        'roles.all' => array(
                            '$exists' => 1
                        )
                    )
                )
            )
        );
        return $buckets;
    }


}
