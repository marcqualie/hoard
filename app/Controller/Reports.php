<?php

namespace Controller;

class Reports extends Base\Page
{

    public function before ()
    {
        if ( ! $this->app->auth->isAdmin())
        {
            header('Location: ' . ($this->app->auth->id ? '/account' : '/login'));
            exit;
        }
    }

    public function req_get ()
    {

        if (empty($this->uri[1]))
        {
            // List reports
            $this->view = 'Reports/List';
            $reports = $this->app->mongo->selectCollection('report')
                ->find()
                ->sort(array(
                    'name' => 1
                ))
                ->asArray();
            foreach ($reports as $index => $report)
            {
                $report['last_report'] = $this->app->mongo->selectCollection('report_' . (String) $report['_id'])
                    ->find()
                    ->sort(array('_id' => -1))
                    ->limit(1)
                    ->getNext();
                $reports[$index] = $report;
            }
            $this->set('reports', $reports);

        }

        // Create new
        elseif ($this->uri[1] === 'create')
        {

            $this->view = 'Reports/Create';

            // Create report
            if ($this->app->request->getMethod() === 'POST')
            {
                $json = $this->app->request->get('report_json');
                $data = json_decode($json, true);
                if ( ! $data)
                {
                    return $this->alert('Invalid JSON', 'error');
                }
                unset($data['_id']);
                if (empty($data['name']))
                {
                    return $this->alert('Report Name is required', 'error');
                }
                $this->app->mongo->selectCollection('report')
                    ->insert($data);
                $this->app->redirect('/report/' . (String) $data['_id']);
            }

            return;
        }

        // Error
        else {
            $this->app->redirect('/reports/');
        }


    }

}
