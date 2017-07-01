<?php

namespace TeePot\WebService\Service;

use TeePot\WebService\Service\Contract\CacheContract;
use App;

class Redis extends CacheContract
{
    protected $redis;
    
    public static function register()
    {
        App::bindSingleton('cache', __CLASS__);
    }

    public function __construct()
    {
        $config = env('REDIS');
        $this->redis = new \Redis();
        $this->redis->pconnect($config['HOST'], $config['PORT']);
        if ($config['PASSWORD']) {
            $this->redis->auth($config['PASSWORD']);
        }
    }

    public function getInstance()
    {
        return $this->redis;
    }

    public function has($key)
    {
        return $this->redis->exists($key);
    }

    public function get($key)
    {
        return $this->redis->get($key);
    }

    public function save($key, $val)
    {
        return $this->redis->set($key, $val);
    }

    public function del($key)
    {
        return $this->redis->del($key);
    }

    public function expire($key, $time)
    {
        $this->redis->setTimeout($key, $time);
    }

    public function saveArray($key, Array $val)
    {
        return $this->redis->hMset($key, $val);
    }

    public function getArray($key)
    {
        return $this->redis->hGetAll($key);
    }
}