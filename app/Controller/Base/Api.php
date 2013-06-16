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
    public function error ($message, $code = 500)
    {
        return array(
            'error' => array(
                'code' => $code,
                'message' => $message
            )
        );
    }

}
