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

        // Just want reporting data
        if (empty($this->uri[2]))
        {
            $report['runs'] = $this->app->mongo
                ->selectCollection('report_' . (String) $report['_id'])
                ->find()
                ->sort(array('_id' => -1))
                ->as_array();
            $this->view = 'Report/Overview';
        }

        // Edit
        elseif ($this->uri[2] === 'edit')
        {
            $this->view = 'Report/Edit';
        }

        // Check if it's a report ID
        else {

            $report_run_id = $this->uri[2];
            $run = $this->app->mongo->selectCollection('report_' . $report_id)
                ->findOne(array(
                    '_id' => new \MongoId($report_run_id)
                ));
            $this->set('run', $run);
            $this->view = 'Report/Run';

        }

        $this->set('report', $report);
    }

}
