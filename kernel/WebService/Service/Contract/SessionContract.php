<?php

namespace TeePot\WebService\Service\Contract;

abstract class SessionContract extends ServiceContract
{
    abstract public function has($name);

    abstract public function get($name);

    abstract public function set($name, $val);

    abstract public function fetch($name);

    abstract public function del($name);

    abstract public function clear();
}