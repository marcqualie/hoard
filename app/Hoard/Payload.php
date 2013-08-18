<?php

namespace Hoard;
use MongoDate;

class Payload
{

    protected $storage = array();

    public function __construct(array $data = array())
    {
        $version = isset($data['v']) ? (int) $data['v'] : 0;
        $this->storage = array(
            'version' => $version,
            'bucket'  => null,
            'event' => '',
            'data' => array(),
            'time' => new MongoDate()
        );

        // Original (deprecated) version
        if ($version === 1) {
            if (isset($data['b'])) {
                $this->storage['bucket'] = $data['b'];
            }
            if (isset($data['e'])) {
                $this->storage['event'] = (String) $data['e'];
            }
            if (isset($data['d'])) {
                $this->storage['data'] = $data['d'];
            }
            if (isset($data['t'])) {
                if (strpos($data['t'], '.') !== false) {
                    list ($sec, $usec) = explode('.', $data['t']);
                    $this->storage['time'] = new MongoDate($sec, $usec);
                } else {
                    $this->storage['time'] = new MongoDate($data['t']);
                }
            }
        }

    }

    /**
     * Check if version is supported by the server
     */
    public function isVersionSupported()
    {
        return $this->storage['version'] === 1 ? true : false;
    }

    /**
     *
     */
    public function __get($key)
    {
        if (isset($this->storage[$key])) {
            return $this->storage[$key];
        }

        return null;
    }

    /**
     * Array helper
     */
    public function asArray()
    {
        return $this->storage;
    }

}
