<?php

namespace Hoard;

class Config {

    private static $data = array();

    /**
     * Load configuration file
     */
    public static function load ($name = 'default')
    {

        // Set defaults
        self::$data = array(
            'mongo.server' => 'mongodb://127.0.0.1:27017/hoard',
            'mongo.options' => array(
                'connect' => false
            )
        );

        // Read from file
        $file = dirname(dirname(__DIR__)) . '/config/' . $name . '.php';
        if (file_exists($file))
        {
            self::$data = include $file;
        }
        return self::$data;
    }

    /**
     * Read config data
     */
    public static function  get ($key)
    {
        return self::$data[$key];
    }

}
