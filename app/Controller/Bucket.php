<?php

namespace Controller;
use Model\Bucket as BucketModel;

class Bucket extends Base\Page
{

    public $bucket;
    public $appkey;

    public function before ()
    {

        // Require Authentication
        if ( ! $this->isLoggedIn()) {
            header('Location: /login/');
            exit;
        }

        // Get app key
        $this->id = isset($this->uri[1]) ? $this->uri[1] : '';
        if (! $this->id) {
            header('Location: /');
            exit;
        }
        $this->bucket = BucketModel::findById($this->id);
        if ($this->bucket === null) {
            // LEGACY: Check mongoid from legacy systems
            $this->bucket = BucketModel::findById(new \MongoId($this->id));
            if ($this->bucket === null) {
                header('Location: /');
                exit;
            }
        }

        // Check if legacy
        if ($this->bucket->legacy) {
            $this->alert('This bucket is running in legacy mode. Please upgrade!');
        }

        // Check action
        $app_action = isset($this->uri[2]) ? $this->uri[2] : '';
        $collection = $this->app->mongo->selectCollection(BucketModel::$collection);
        switch ($app_action) {
            case 'save':
                $alias_string = $this->app->request->get('alias');
                $explode = explode(',', $alias_string);
                $aliases = array();
                foreach ($explode as $alias) {
                    $alias = trim($alias);
                    if (preg_match(BucketModel::$regex_id, $alias)) {
                        $aliases[] = $alias;
                    }
                }
                $this->bucket->alias = $aliases;
                $this->bucket->description = $this->app->request->get('description');
                $this->bucket->save();
                break;
            case 'delete':
                $collection->remove(array(
                    '_id' => $this->bucket->id
                ));
                $this->app->mongo->selectCollection($this->bucket->event_collection)->drop();
                header('Location: /');
                exit;
                break;
            case 'empty':
                $this->app->mongo->selectCollection($this->bucket->event_collection)->drop();
                header('Location: /bucket/' . $this->id);
                exit;
                break;
        }

    }

    public function req_get ()
    {
        $this->bucket->latest_event = $this->app->mongo->selectCollection($this->bucket->event_collection)->find()->sort(array('t' => -1))->limit(1)->next()->current();
        $this->bucket->stats = $this->app->mongo->command(array('collStats' => $this->bucket->event_collection));
        $this->bucket->rps = $this->app->mongo->selectCollection($this->bucket->event_collection)->find(
            array(
                't' => array(
                    '$gte' => new \MongoDate(time() - 86400)
                )
            )
        )->count() / 86400;
        $this->set('bucket', $this->bucket);
        $this->title = 'Hoard - ' . $this->bucket->description;

    }

}
