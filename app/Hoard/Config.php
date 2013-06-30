<?php

namespace Hoard;
use Utils;

class Config {


    /**
     * Get Instance
     */
    public static $instances = array();
    public static function instance($name = 'default')
    {
        if (isset(self::$instances[$name])) {
            return self::$instances[$name];
        }
        $instance = new Config();
        self::$instances[$name] = $instance;
        return $instance;
    }


    /**
     * Load configuration file
     */
    public $data = array();
    public function load($name = 'default')
    {

        // Set defaults
        $this->data = array(
            'mongo.server' => 'mongodb://127.0.0.1:27017/hoard',
            'mongo.options' => array(
                'connect' => false
            )
        );

        // Read from file
        $this->data = $this->loadByName($name);
        return $this->data;
    }


    /**
     * Load file by name
     */
    public function loadByName($name)
    {

        // Extend Helper
        $self = $this;
        $extend = function ($name, $data) use ($self) {
            $original = $self->loadByName($name);
            return is_array($original) ? Utils::array_merge_recursive_distinct($original, $data) : $data;
        };

        // Load File
        $data = array();
        $file = dirname(__DIR__) . '/Config/' . $name . '.php';
        if (file_exists($file)) {
            $data = include $file;
        }
        return $data;
    }


    /**
     * Read config data
     */
    public function get($key)
    {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return null;
    }

}
