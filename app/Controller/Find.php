<?php

namespace Controller;
use Model\Bucket as BucketModel;

class Find extends Base\Page
{

    public function req_get ()
    {

        header('Content-Type: text/plain');
        $params = $_GET + $_POST;

        // Retrict Access to logged in users
//      if ( ! Auth::$id)
//      {
//          echo '{"error":"Authentication Required"}';
//          exit;
//      }

        // App Key Required, Secret too in future
        $bucket_id = $this->app->request->get('bucket') ?: $this->uri[1];
        if (empty($bucket_id)) {
            return $this->jsonError('Bucket ID is Required', 400);
        }
        $bucket = BucketModel::findById($bucket_id);
        if (! $bucket) {
            return $this->jsonError('Invalid Bucket ID', 404);
        }

        // Vars
        //$event = isset($this->uri[1]) ? $this->uri[1] : false;
        $event = $this->app->request->get('event') ?: '';
        $limit = isset($params['limit']) ? (int) $params['limit'] : 10;
        if ($limit < 1) $limit = 10;

        // Where
        $where = array();
        if ($event) {
            $where['e'] = $event;
        }
        /*
        if ($bucket) {
            $where['appkey'] = $bucket;
        } else {
            $app_keys = array();
            foreach (Auth::$buckets as $k => $app) {
                $app_keys[] = $app['appkey'];
            }
            $where['appkey'] = array('$in' => $app_keys);
        }
        */
        if (! empty($params['query'])) {
            $json = $this->json2array($params['query'], true);
            if (is_scalar($json) || ! is_array($json)) {
                $json = array(
                    '$e' => $params['query']
                );
            }
            foreach ($json as $k => $v) {
                // Specials
                if ($k === '$e') {
                    $where['e'] = $v;
                    continue;
                }
                if (is_array($v)) {
                    foreach ($v as $_k1 => $_v1) {
                        if ($_k1 === '$regex') {
                            $v[$_k1] = new MongoRegex($_v1);
                        }
                    }
                }
                $where['d.' . $k] = $v;
            }
        }
//      print_r($where); exit;

        if (isset($where['_id'])) {
            $where['_id'] = new MongoId($where['_id']);
        }

        // Fields
        $fields = array();
        if (! empty($params['fields'])) {
            $explode = explode(',', $params['fields']);
            foreach ($explode as $field) {
                $field = trim($field);
                $fields['d.' . $field] = 1;
            }
        }
        if ($fields) {
            $fields['t'] = 1;
            $fields['e'] = 1;
        }

        // Sort
        $sort = array();
        if (! empty($params['sort'])) {
            $json = $this->json2array($params['sort'], true);
            if (! $json) {
                preg_match('/^([^:]+)(:([\-]*1))?$/', $params['sort'], $matches);
                if (isset($matches[1])) {
                    $order = isset($matches[3]) && $matches[3] === '-1' ? -1 : 1;
                    $json = array(
                        $matches[1] => $order
                    );
                }
            }
            foreach ($json as $k => $v) {
                if ($k === '$time') {
                    $sort['t'] = $v;
                } else {
                    $sort["d." . $k] = $v;
                }
            }
        }
        if (! $sort) {
            $sort['t'] = -1;
        }
//        print_r($sort);

        // Find Data
        // Save Data to log
        try {
            $collection = $this->app->mongo->selectCollection($bucket->event_collection);
            try {
                $cursor = $collection
                    ->find($where, $fields)
                    ->limit($limit);
                if ($sort) {
                    $cursor->sort($sort);
                }
                $data = array();
                foreach ($cursor as $row) {
                    $row['_id'] = (String) $row['_id'];
//                  $row['date'] = (array) $row['t'];
                    $data[] = $row;
                }
                echo json_encode($data);
            } catch (MongoCursorException $e) {
                echo '{"error":"Cursor Exception"}';
                exit;
            }
            exit;
        }

        // Could not connect
        catch (MongoConnectionException $e) {
            echo '{"error":"Connection Exception"}';
            exit;
        }

        // Output
        exit;

    }

    public function json2array ($str)
    {
        try {
            $json = json_decode($str, true);
            if ($json) {
                return $json;
            }
        } catch (Exception $e) {
        }

        return array();
    }

}
