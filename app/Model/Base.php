<?php

namespace Model;

class Base
{

    public static $collection;

    protected $app;
    public $id = null;
    protected $data;

    public function __construct(array $data = array())
    {
        if ($data) {
            $this->id = $data['_id'];
            $this->data = $data;
        }
        $this->init();
    }


    /**
     * Initialize Function to easily extend contructor
     */
    public function init()
    {

    }


    /**
     * Get variables
     */
    public function __get($field)
    {
        if (isset($this->data[$field])) {
            return $this->data[$field];
        }
        return null;
    }
    public function __isset($field)
    {
        return isset($this->data[$field]);
    }


    /**
     * Set Data
     *
     * Only data described in the schema will be saved back to database
     */
    public function __set($field, $value)
    {
        $this->data[$field] = $value;
        return $value;
    }


    /**
     * Get Schema for this Model
     */
    public function getSchema()
    {
        return array(
            '_id' => 'MongoId',
            'created' => 'MongoDate',
            'updated' => 'MongoDate'
        );
    }


    /**
     * Get application instance
     */
    public static function getApp()
    {
        return \Hoard\Application::$app;
    }


    /**
     * Find all instamces matching a query
     */
    public static function find(array $query = array())
    {
        $app = self::getApp();
        $collection = $app->mongo->selectCollection(static::$collection);
        $results = $collection->find($query);
        $models = array();
        $model_name = get_called_class();
        foreach ($results as $data) {
            $models[] = new $model_name($data);
        }
        return $models;
    }


    /**
     * Find instance by ID
     */
    public static function findById($id)
    {
        $app = self::getApp();
        $collection = $app->mongo->selectCollection(static::$collection);
        $data = $collection->findOne(array(
            '_id' => $id
        ));
        if ($data) {
            $model_name = get_called_class();
            return new $model_name($data);
        }
        return null;
    }

}
