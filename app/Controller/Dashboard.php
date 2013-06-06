<?php

namespace Controller;

class Dashboard extends Base\Page
{

    public function before ()
    {
        if ( ! $this->isLoggedIn())
        {
            header('Location: /login/');
            exit;
        }
        $this->title = 'Hoard - Dashboard';
    }

    public function req_get ()
    {

        $collection = $this->app->mongo->selectCollection('app');
        $buckets = $this->app->auth->user['buckets'];
        foreach ($buckets as &$bucket)
        {

            // Get stats from raw collection data
            $stats_raw = $this->app->mongo->command(array(
                'collStats' => 'event_' . $bucket['appkey']
            ));

            $stats = array();
            $bucket['records'] = isset ($stats_raw['count']) ? (int) $stats_raw['count'] : 0;
            $bucket['rps'] = 0;
            $bucket['rps'] = (float) $this->app->mongo->selectCollection('event_' . $bucket['appkey'])
                ->find(
                    array('t' => array('$gte' => new \MongoDate(time() - 300)))
                ,   array('_id' => 0, 't' => 1)
                    )
                ->count() / 300;
        }
        \Utils::array_sort($buckets, '!rps');
        $this->set('buckets', $buckets);

    }

}
