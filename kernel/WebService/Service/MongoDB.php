<?php

namespace TeePot\WebService\Service;

use TeePot\WebService\Service\Contract\DBContract;
use App;
use MongoDB\Client;

class MongoDB extends DBContract
{
    protected $client;
    protected $db;
    protected $collections = [];

    public static function register()
    {
        App::bindSingleton('db', __CLASS__);
    }

    public function getInstance()
    {
        return $this->client;
    }

    public function __construct()
    {
        $config = env('DB');
        $this->client = new Client($config['CONN_STR']);
        $this->db = $this->client->selectDatabase($config['DB_NAME']);
    }

    public function selectTable($collection)
    {
        return isset($this->collections[$collection]) ?
            $this->collections[$collection] :
            $this->collections[$collection] =
                $this->db->selectCollection($collection);
    }

    public function __call($name, $args)
    {
        return $this->db->$name(...$args);
    }
}