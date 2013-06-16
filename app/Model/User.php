<?php

namespace Model;

class User extends Base
{

    public static $collection = 'user';
    public $apikey_limit = 5;

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
        \Utils::model_sort($buckets, 'description');
        return $buckets;
    }


    /**
     * Create API Key
     */
    public function createApiKey()
    {
        if (count($this->apikeys) < $this->apikey_limit) {
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


}
