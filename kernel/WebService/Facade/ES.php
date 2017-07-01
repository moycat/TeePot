<?php

namespace TeePot\WebService\Facade;

use TeePot\WebService\Facade\Contract\FacadeContract;

/**
 * Class ES
 * @package Facade
 * @contract Service\Contract\DB
 */
class ES extends FacadeContract
{
    protected static function getServiceName()
    {
        return 'es';
    }
}