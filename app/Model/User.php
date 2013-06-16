<?php

namespace Model;

class User extends Base
{

    public static $collection = 'user';


    /**
     * User Schema
     */
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


    /**
     * Create API Key
     */
    public function createApiKey()
    {
        $key = substr(sha1(uniqid() . uniqid() . uniqid()), 0, 24);
        $apikeys = $this->apikeys;
        $apikeys[$key] = array(
            'active' => true,
            'requests' => 0,
            'buckets' => array(),
            'created' => new \MongoDate()
        );
        $this->apikeys = $apikeys;
        $this->save();
    }


}
