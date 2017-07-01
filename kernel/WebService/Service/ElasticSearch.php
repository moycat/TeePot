<?php

namespace TeePot\WebService\Service;

use TeePot\WebService\Service\Contract\DBContract;
use App;
use Elasticsearch\ClientBuilder;

class ElasticSearch extends DBContract
{
    protected $client;
    protected $index;

    public static function register()
    {
        App::bindSingleton('es', __CLASS__);
    }

    public function getInstance()
    {
        return $this->client;
    }

    public function __construct()
    {
        $config = env('ES');
        $this->client = ClientBuilder::create()
            ->setHosts($config['CONN_STR'])
            ->build();
        $this->index = $config['INDEX'];
    }

    public function selectTable($table)
    {
        return $this->client->index($table);
    }

    public function genQuery($type, Array $body)
    {
        if (is_array($type)) {
            $type = implode(',', $type);
        }
        return [
            'index' => $this->index,
            'type'  => $type,
            'body'  => $body
        ];
    }

    public function __call($name, $args)
    {
        return $this->client->$name(...$args);
    }
}