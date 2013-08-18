<?php

namespace Hoard\Test;
use MongoMinify\Client;
use Model\Bucket;
use Model\User;

class TestCase extends \PHPUnit_Framework_TestCase
{

    protected $app;
    protected $client;
    protected $mongo;
    protected $server;
    protected $apikey;

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->approot = dirname(dirname(dirname(__DIR__)));
    }


    /**
     * Test setup
     */
    public function setUp()
    {

        // Create Environment
        $db = $this->getDb();
        $db->selectCollection(Bucket::$collection);

        // Empty Database
        $this->emptyDb();

        // Create user
        $user = User::create(array(
            'email' => 'test@hoardhq.com'
        ));
        $apikey = $user->createApiKey();
        $apikeys = array_keys($user->apikeys);
        $this->apikey = $apikeys[0];

    }


    /**
     * Empty Database
     */
    public function emptyDb()
    {
        $collections = $this->mongo->native->getCollectionNames();
        foreach ($collections as $collection) {
            $this->mongo->selectCollection($collection)->remove();
        }
    }


    /**
     * Get database instance
     * @return MongoMinify\Client
     */
    public function getDb()
    {
        $this->client = new Client();
        $this->mongo = $this->client->selectDb('hoard_test');

        return $this->mongo;
    }


    /**
     * Create test bucket
     */
    public function createTestBucket()
    {
        $bucket = Bucket::create(array(
            '_id' => '51d077a88dff0',
            'alias' => array(
                'test-bucket'
            )
        ));
        return $bucket;
    }


    /**
     * Make internal request to http server
     * @param  string $uri
     * @return bool
     */
    public function makeApiRequest($method = 'GET', $uri, array $post = array())
    {

        // Add API key to request params
        $uri .= (strpos('?', $uri) === -1 ? '?' : '&') . 'apikey=' . $this->apikey;

        // Do normal raw request
        return $this->makeRawRequest($method, $uri, $post);

    }


    /**
     * Make raw request to internal HTTP server
     */
    public function makeRawRequest($method = 'GET', $uri, array $post = array())
    {

        // Override server params
        $_SERVER['HTTP_HOST'] = 'hoard.dev';
        $_SERVER['REQUEST_URI'] = $uri;
        $_SERVER['REQUEST_METHOD'] = $method;
        $_SERVER['DOCUMENT_ROOT'] = $this->approot;

        // Query String
        $parsed = parse_url('http://' . $_SERVER['HTTP_HOST'] . $uri);
        if (isset($parsed['query'])) {
            parse_str(html_entity_decode($parsed['query']), $query);
            $_GET = $query;
            $_POST = $post;
        }
        $queryString = http_build_query($query, '', '&');
        $_SERVER['REQUEST_URI'] = $parsed['path'] . ('' !== $queryString ? '?' . $queryString : '');
        $_SERVER['QUERY_STRING'] = $queryString;

        // Load front controller with new server variables
        ob_start();
        $response = include $this->approot . '/public/index.php';
        ob_end_clean();

        return $response;
    }



}
