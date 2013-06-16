<?php

namespace Controller;
use Model\Bucket;

class Buckets extends Base\Page
{

    public function before ()
    {
        if ( ! $this->isLoggedIn())
        {
            header('Location: /login/');
            exit;
        }
        $this->title = 'Hoard - My Buckets';
    }

    public function req_post ()
    {

        if ($this->app->request->get('action') === 'create_bucket')
        {

            $name = $this->app->request->get('bucket_name');
            $id = strtolower($name);
            $id = str_replace(array(' ', '_'), '-', $id);
            $pattern = Bucket::$regex_id;

            // No name is specified
            if (! $id) {
                $this->alert('You need to specify an ID');
            }

            // Verify name (Names are IDs now)
            elseif (! preg_match($pattern, $id)) {
                $this->alert('Invalid ID. Please match <strong>' . $pattern . '</strong>');
            }

            // Make sure name is unique
            elseif (Bucket::exists($id)) {
                $this->alert('Bucket name must be unique across cluster');
            }

            // Name matches, continue
            else {
                $data = array(
                    '_id' => $id,
                    'description' => $name,
                    'roles' => array(
                        $this->app->auth->id => 'owner'
                    ),
                    'created' => new \MongoDate(),
                    'updated' => new \MongoDate()
                );
                $bucket = Bucket::create($data);
                if ($bucket) {
                    $this->alert('Your app was created');
                } else {
                    $this->alert('There was a problem creating your bucket', 'error');
                }
            }

        }

        // Fallback to get
        $buckets = Bucket::find(array(
            '$or' => array(
                array(
                    'roles.' . $this->app->auth->id => array(
                        '$exists' => 1
                    )
                ),
                array(
                    'roles.all' => array(
                        '$exists' => 1
                    )
                )
            )
        ));
        $this->app->auth->user['buckets'] = $buckets;
        return $this->req_get();

    }

    public function req_get ()
    {

        $buckets = $this->app->auth->user['buckets'];
        $totals = array('records' => 0, 'rps' => 0, 'storage' => 0, 'storage_index' => 0);
        foreach ($buckets as &$bucket)
        {

            // Get stats from raw collection data
            $stats_raw = $this->app->mongo->command(array(
                'collStats' => $bucket->event_collection
            ));

            // Force index creation if not already there (temp hack, should be done on creation)
            if ( ! isset($stats_raw['indexSizes']['t_-1']))
            {
                $this->app->mongo->selectCollection($bucket->event_collection)->ensureIndex(array('t' => -1));
            }

            $stats = array();
            $bucket->records = isset ($stats_raw['count']) ? (int) $stats_raw['count'] : 0;
            $bucket->rps = 0;
            $bucket->rps = (float) $this->app->mongo->selectCollection($bucket->event_collection)
                ->find(
                    array('t' => array('$gte' => new \MongoDate(time() - 300))),
                    array('_id' => 0, 't' => 1)
                    )
                ->count() / 300;
            $bucket->storage = isset ($stats_raw['size']) ? (int) $stats_raw['size'] : 0;
            $bucket->storage_index = isset ($stats_raw['totalIndexSize']) ? (int) $stats_raw['totalIndexSize'] : 0;
            $bucket->storage_avg = isset($stats_raw['avgObjSize']) ? (int) $stats_raw['avgObjSize'] : 0;

            // Calculate totals
            $totals['records'] += $bucket->records;
            $totals['rps'] += $bucket->rps;
            $totals['storage'] += $bucket->storage;
            $totals['storage_index'] += $bucket->storage_index;
        }
        \Utils::model_sort($buckets, '!records');
        $this->set('totals', $totals);
        $this->set('buckets', $buckets);

    }

}
