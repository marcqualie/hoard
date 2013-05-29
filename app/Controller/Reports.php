<?php

namespace Controller;

class Reports extends Base\Page
{

    public function req_get ()
    {

        $reports = iterator_to_array($this->app->mongo->selectCollection('report')->find());
        $this->set('reports', $reports);

    }

}
