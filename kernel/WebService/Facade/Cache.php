<?php

namespace TeePot\WebService\Facade;

use TeePot\WebService\Facade\Contract\FacadeContract;

/**
 * Class Cache
 * @package Facade
 * @contract Service\Contract\Cache
 */
class Cache extends FacadeContract
{
    protected static function getServiceName()
    {
        return 'cache';
    }
}