<?php

namespace Controller;

class Bucket extends Base\Page
{

    public $bucket;
    public $appkey;

    public function before ()
    {

        // Require Authentication
        if ( ! $this->isLoggedIn())
        {
            header('Location: /login/');
            exit;
        }

        // Get app key
        $this->id = isset($this->uri[1]) ? $this->uri[1] : '';
        if ( ! $this->id)
        {
            header('Location: /');
            exit;
        }
        $this->bucket = \Model\Bucket::findById($this->id);
        if ($this->bucket === null) {
            // LEGACY: Check mongoid from legacy systems
            $this->bucket = \Model\Bucket::findById(new \MongoId($this->id));
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
        switch ($app_action)
        {
            case 'delete':
//                $this->app->mongo->selectCollection(Model\Bucket::$collection)->remove(array('appkey' => $this->appkey));
//                $this->app->mongo->selectCollection('event_' . $this->appkey)->drop();
//                header('Location: /');
                exit;
                break;
            case 'empty':
//                $this->app->mongo->selectCollection('event_' . $this->appkey)->remove();
//                header('Location: /bucket/' . $this->appkey);
                exit;
                break;
        }

    }

    public function req_get ()
    {
        $this->bucket->latest_event = $this->app->mongo->selectCollection('event_' . $this->bucket->appkey)->find()->sort(array('t' => -1))->limit(1)->next()->current();
        $this->bucket->stats = $this->app->mongo->command(array('collStats' => 'event_' . $this->bucket->appkey));
        $this->bucket->rps = $this->app->mongo->selectCollection('event_' . $this->bucket->appkey)->find(
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
