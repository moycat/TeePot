<?php

namespace TeePot\WebService\Service\Contract;

abstract class CacheContract extends ServiceContract
{
    abstract public function has($key);

    abstract public function get($key);

    abstract public function save($key, $val);

    abstract public function del($key);

    abstract public function expire($key, $time);

    abstract public function saveArray($key, Array $val);
    
    abstract public function getArray($key);

    public function delArray($key)
    {
        $this->del($key);
    }
}