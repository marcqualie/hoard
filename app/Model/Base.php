<?php

namespace Model;
use Utils;

class Base
{

    public static $collection;

    protected $app;
    protected $data;

    public function __construct(array $data = array())
    {
        if (! static::$collection) {
            throw new Exception('Model has not defined a collection');
        }
        if ($data) {
            $this->data = $data;
        }
        if ($this->id) {
            $data = self::getApp()->mongo->selectCollection(static::$collection)->findOne(array(
                '_id' => $this->id
            ));
            if (! empty($data['_id'])) {
                $this->data = Utils::array_merge_recursive_distinct($data, $this->data);
            }
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
        if ($field === 'id') {
            $field = '_id';
        }
        if (isset($this->data[$field])) {
            return $this->data[$field];
        }

        return null;
    }
    public function __isset($field)
    {
        if ($field === 'id') {
            $field = '_id';
        }

        return isset($this->data[$field]);
    }

    /**
     * Set Data
     *
     * Only data described in the schema will be saved back to database
     */
    public function __set($field, $value)
    {
        if ($field === 'id') {
            $field = '_id';
        }
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
        if (empty($document['created'])) {
            $document['created'] = new \MongoDate();
        }
        $document['updated'] = new \MongoDate();
        $collection = self::getApp()->mongo->selectCollection(static::$collection);
        $save = $collection->save($document);
        if (isset($save['ok']) && (int) $save['ok'] === 1) {
            $this->data = $document;

            return true;
        }

        return false;
    }

    /**
     * As array
     */
    public function asArray()
    {
        return $this->data;
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
    public static function findOne(array $query = array())
    {
        $objects = static::find($query);

        return ! empty($objects[0]) ? $objects[0] : null;
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
