<?php

namespace TeePot\WebService\Facade;

use TeePot\WebService\Facade\Contract\FacadeContract;

/**
 * Class Request
 * @package Facade
 * @contract Service\Contract\Request
 */
class Request extends FacadeContract
{
    protected static function getServiceName()
    {
        return 'request';
    }
}