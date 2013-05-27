<?php

namespace Controller;

class Error extends Base\Page
{

    public function req_get()
    {

        // Set default error message
        if ( ! isset($this->var['code']) || ! isset($this->var['message']))
        {
            $this->var['code'] = 404;
            $this->var['message'] = 'Page Not Found';
        }

    }

}
