<?php

namespace Hoard;

class ApiResponse
{
    public $code = 200;
    public $data = array();
    public $meta = array();
    public $debug = array();
    public $error = null;

    /**
     * Create Instance
     */
    public function __construct ($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

}
