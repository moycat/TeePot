<?php

namespace TeePot\WebService\Facade;

use TeePot\WebService\Facade\Contract\FacadeContract;

/**
 * Class DB
 * @package Facade
 * @contract Service\Contract\DB
 */
class DB extends FacadeContract
{
    protected static function getServiceName()
    {
        return 'db';
    }
}