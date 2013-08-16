<?php

namespace Model;

class User extends Base
{

    public static $collection = 'user';
    public $apikey_limit = 10;

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
                'name' => $key,
                'active' => true,
                'requests' => 0,
                'buckets' => array(),
                'created' => new \MongoDate(),
                'updated' => new \MongoDate()
            );
            $this->apikeys = $apikeys;
            $this->save();
        }
    }

    /**
     * Update API Key
     */
    public function updateApiKey(array $data = array())
    {
        $update = array();
        if (! isset($data['id'])) {
            throw new \Exception('Invalid API Key');
        }
        $id = $data['id'];
        $apikeys = $this->apikeys;
        if (isset($data['name'])) {
            $apikeys[$id]['name'] = $data['name'];
        }
        if (isset($data['active'])) {
            $apikeys[$id]['active'] = (int) $data['active'] ? true : false;
        }
        if ($apikeys !== $this->apikeys) {
            $apikeys[$id]['updated'] = new \MongoDate();
            $this->apikeys = $apikeys;
            $this->save();
        }
    }

    /**
     * Delete API Key
     */
    public function deleteApiKey($id)
    {
        if (isset($this->apikeys[$id])) {
            $apikeys = $this->apikeys;
            unset($apikeys[$id]);
            $this->apikeys = $apikeys;
            $this->save();
        }
        return false;
    }

    /**
     * Helper for updating user passwords
     */
    public function setPassword($password)
    {
        if (strlen($password) < 4) {
            return 'Password must be longer than 4 chars';
        }
        $this->password = $this->getApp()->auth->password($password);
        $this->save();

        return 0;
    }

}
