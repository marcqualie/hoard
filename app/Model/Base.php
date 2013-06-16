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
        return false;
    }

}
