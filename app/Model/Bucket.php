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
        if ((is_object($this->id) && get_class($this->id) === 'MongoId') || $this->appkey) {
            $this->legacy = true;
        }
        if (empty($this->data['description'])) {
            $this->data['description'] = $this->name ?: $this->id;
        }
    }


    /**
     * Generate ID
     */
    public function generateId()
    {
        return uniqid();
    }


    /**
     * Find instance by ID
     */
    public static function findById($id)
    {
        $app = self::getApp();
        $collection = $app->mongo->selectCollection(static::$collection);
        $data = $collection->findOne(
            array(
                '$or' => array(
                    array('_id' => $id),
                    array('alias' => $id)
                )
            )
        );
        if ($data) {
            $model_name = get_called_class();
            return new $model_name($data);
        }
        return null;
    }


    /**
     * Check if object exists
     */
    public static function exists($id)
    {
        $collection = self::getApp()->mongo->selectCollection(static::$collection);
        return $collection->find(
            array(
                '$or' => array(
                    array('_id' => $id),
                    array('alias' => $id)
                )
            ),
            array(
                '_id' => 1
            )
        )->count() > 0 ? true : false;
    }


    /**
     * Bucket Schema
     */
    public function getSchema()
    {
        return array(
            '_id' => 'String',
            'description' => 'String',
            'alias' => 'String',
            'roles' => 'Hash',
            'created' => 'MongoDate',
            'updated' => 'MongoDate'
        );
    }


    /**
     * Add Role
     */
    public function addRole($user_id, $role)
    {
        $roles = $this->roles;
        $roles[$user_id] = $role;
        $this->roles = $roles;
        $this->save();
    }

}
