<?php

namespace TeePot\WebService\Facade\Contract;

use \Exception;

abstract class FacadeContract
{
    protected static $me;

    /**
     * @throws Exception when not overridden
     * @return string
     */
    protected static function getServiceName()
    {
        throw new Exception("Facade Not Completed");
    }

    public static function getInstance()
    {
        $name = static::getServiceName();
        if (!isset(static::$me[$name])) {
            static::$me[$name] = app()->make(static::getServiceName());
        }
        return static::$me[$name];
    }

    public static function __callStatic($method, $args)
    {
        $instance = static::getInstance();

        if (is_null($instance)) {
            throw new Exception('Fail to Instantiate');
        }

        return $instance->$method(...$args);
    }
}