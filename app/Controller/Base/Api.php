<?php

namespace Controller\Base;

class Api {

    public $app;


    /**
     * Main API Execution Method
     * @return [type] [description]
     */
    public function exec ()
    {
    }


    /**
     * Error Message
     */
    public function error ($code, $message)
    {
        return array(
            'error' => array(
                'code' => $code,
                'message' => $message
            )
        );
    }

}
