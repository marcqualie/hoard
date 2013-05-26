<?php

namespace Controller;

class Index extends Base\Page
{

    public function before ()
    {
        if ( ! $this->isLoggedIn())
        {
            header('Location: /login/');
            exit;
        }
        $this->set('title', 'Buckets - Hoard');
    }

    public function req_post ()
    {

        if ($_POST['action'] === 'create_bucket')
        {
            $name = preg_replace('/[^a-zA-Z0-9\.\-_ \(\)\'"]/', '', $_POST['app-name']);
            if ($name)
            {
                $appkey = uniqid();
                $secret = sha1($appkey . uniqid() . $_SERVER['REMOTE_ADDR'] . rand(0, 999999));
                $data = array(
                    'name' => $name,
                    'appkey' => $appkey,
                    'secret' => $secret,
                    'roles' => array(
                        Auth::$id => 'owner'
                    ),
                    'created' => new MongoDate(),
                    'updated' => new MongoDate()
                );
                $this->app->mongo->selectCollection('app')->insert($data);
                $this->alert('Your app was created');
            }
            else
            {
                $this->alert('You need to specify a name');
            }
        }

        // Fallback to get
        $cursor = $this->app->mongo->selectCollection('app')->find(array(
            '$or' => array(
                array(
                    'roles.' . Auth::$id => array(
                        '$exists' => 1
                    )
                ),
                array(
                    'roles.all' => array('$exists' => 1)
                )
            )
        ));
        Auth::$buckets = iterator_to_array($cursor);
        return $this->req_get();

    }

    public function req_get ()
    {

        $collection = $this->app->mongo->selectCollection('app');

        $buckets = $this->app->auth->user['buckets'];
        $totals = array('records' => 0, 'rps' => 0, 'storage' => 0, 'storage_index' => 0);
        foreach ($buckets as &$bucket)
        {

            // Get stats from raw collection data
            $stats_raw = $this->app->mongo->command(array(
                'collStats' => 'event_' . $bucket['appkey']
            ));

            // Force index creation if not already there (temp hack, should be done on creation)
//          print_r($stats_raw);
            if ( ! isset($stats_raw['indexSizes']['t_-1']))
            {
                $this->app->mongo->selectCollection('event_' . $bucket['appkey'])->ensureIndex(array('t' => -1));
            }

            $stats = array();
            $bucket['records'] = isset ($stats_raw['count']) ? (int) $stats_raw['count'] : 0;
            $bucket['rps'] = 0;
            $bucket['rps'] = (float) $this->app->mongo->selectCollection('event_' . $bucket['appkey'])
                ->find(
                    array('t' => array('$gte' => new \MongoDate(time() - 300)))
                ,   array('_id' => 0, 't' => 1)
                    )
                ->count() / 300;
            $bucket['storage'] = isset ($stats_raw['size']) ? (int) $stats_raw['size'] : 0;
            $bucket['storage_index'] = isset ($stats_raw['totalIndexSize']) ? (int) $stats_raw['totalIndexSize'] : 0;
            $bucket['storage_avg'] = isset($stats_raw['avgObjSize']) ? (int) $stats_raw['avgObjSize'] : 0;

            // Calculate totals
            $totals['records'] += $bucket['records'];
            $totals['rps'] += $bucket['rps'];
            $totals['storage'] += $bucket['storage'];
            $totals['storage_index'] += $bucket['storage_index'];
        }
        \Utils::array_sort($buckets, '!records');
        $this->set('totals', $totals);
        $this->set('buckets', $buckets);

    }

}
