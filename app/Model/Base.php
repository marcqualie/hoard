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
     * Generate ID
     */
    public function generateId()
    {
        return new \MongoId();
    }


    /**
     * Save data to database
     */
    public function save()
    {
        $schema = $this->getSchema();
        $document = array();
        foreach ($schema as $field => $type) {
            if (isset($this->data[$field])) {
                $document[$field] = $this->data[$field];
            }
        }
        if (empty($document['_id'])) {
            $document['_id'] = $this->generateId();
        }
        $collection = self::getApp()->mongo->selectCollection(static::$collection);
        $save = $collection->save($document);
        return isset($save['ok']) && (int) $save['ok'] === 1 ? true : false;
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


    /**
     * Check if object exists
     */
    public static function exists($id)
    {
        $collection = self::getApp()->mongo->selectCollection(static::$collection);
        return $collection->find(
            array('_id' => $id),
            array('_id' => 1)
        )->count() > 0 ? true : false;
    }


    /**
     * Create new Object
     */
    public static function create(array $data = array())
    {
        $model_name = get_called_class();
        $instance = new $model_name($data);
        $save = $instance->save();
        if (! $save) {
            return false;
        }
        return $instance;
    }

}
