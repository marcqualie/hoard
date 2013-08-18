<?php

namespace Controller\Api;

use Model\Bucket;

class Query extends \Controller\Base\Api
{

    public function exec()
    {

        $request = $this->app->request;

        // Bucket ID is required
        $bucket_id = $request->get('bucket');
        if (empty($bucket_id)) {
            return $this->error('Bucket ID is Required', 400);
        }
        $bucket = Bucket::findById($bucket_id);
        if (! $bucket) {
            return $this->error('Invalid Bucket ID', 404);
        }

        // Grab request params
        $event = $request->get('event') ?: '';
        $limit = (int) $request->get('limit') ?: 10;

        // Where
        $where_param = $request->get('where');
        $where = array();
        if ($event) {
            $where['e'] = $event;
        }
        if (! empty($where_param)) {
            $json = json_decode($where_param, true);
            if (is_scalar($json) || ! is_array($json)) {
                $json = array(
                    '$e' => $where_param
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

        // Fields
        $fields_param = $request->get('fields');
        $fields = array();
        if (! empty($fields_param)) {
            $explode = explode(',', $fields_param);
            foreach ($explode as $field) {
                $field = trim($field);
                $fields['d.' . $field] = 1;
            }
        }
        if ($fields) {
            $fields['t'] = 1;
            $fields['e'] = 1;
        }

        // Sorting
        $sort_param = $request->get('sort');
        $sort = array();
        if (! empty($sort_param)) {
            $json = json_decode($sort_param, true);
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
                    $sort['d.' . $k] = $v;
                }
            }
        }
        if (! $sort) {
            $sort['t'] = -1;
        }

        // Grab from database
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
                    $item = array(
                        'id' => (String) $row['_id'],
                        'event' => $row['e'],
                        'data' => $row['d'],
                        'time' => [$row['t']->sec, $row['t']->usec],
                    );
                    $data[] = $item;
                }

            } catch (MongoCursorException $e) {
                return $this->error('Database Write Error', 503);
            }

        } catch (MongoConnectionException $e) {
            return $this->error('Database Connection Error', 503);
        }

        // Output Results
        return array(
            'data' => $data,
            'meta' => array(
                'where' => $where,
                'fields' => $fields,
                'limit' => $limit,
                'sort' => $sort,
            )
        );

    }
}
