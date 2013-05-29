<?php

namespace Controller;

class Report extends Base\Page
{

    public function req_get ()
    {

        $report_id = isset($this->uri[1])
            ? $this->uri[1]
            : exit('Report ID Required');

        $report = $this->app->mongo
            ->selectCollection('report')
            ->findOne(array(
                '_id' => new \MongoId($report_id)
            ));
        $this->set('report', $report);

    }

}
