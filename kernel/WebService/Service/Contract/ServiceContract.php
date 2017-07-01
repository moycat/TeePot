<?php

namespace TeePot\WebService\Service\Contract;

use \Exception;

abstract class ServiceContract
{
    public static function register()
    {
        throw new Exception('Service Not Completed');
    }

    public function getInstance()
    {
        return null;
    }
}