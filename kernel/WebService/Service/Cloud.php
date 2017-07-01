<?php

namespace TeePot\WebService\Service;

use TeePot\WebService\Service\Contract\CacheContract;
use TeePot\GlobalDataClient;
use App;

class Cloud extends CacheContract
{
    /**
     * @var GlobalDataClient
     */
    protected $cloud = null;

    public static function register()
    {
        App::bindSingleton('cache', __CLASS__);
    }

    public function __construct()
    {
        $this->cloud = new GlobalDataClient('127.0.0.1:2207');

    }

    public function getInstance()
    {
        return $this->cloud;
    }

    public function has($key)
    {
        return isset($this->cloud->$key);
    }

    public function get($key)
    {
        return $this->cloud->$key;
    }

    public function save($key, $val)
    {
        return $this->cloud[$key] = $val;
    }

    public function del($key)
    {
        unset($this->cloud->$key);
    }

    public function expire($key, $time)
    {
        $this->cloud->expire($key, $time);
    }

    public function saveArray($key, Array $val)
    {
        return $this->cloud->$key = $val;
    }

    public function getArray($key)
    {
        return $this->cloud->$key;
    }
}