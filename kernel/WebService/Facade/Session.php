<?php

namespace TeePot\WebService\Facade;

use TeePot\WebService\Facade\Contract\FacadeContract;

/**
 * Class Session
 * @package Facade
 * @contract Service\Contract\Session
 */
class Session extends FacadeContract
{
    protected static function getServiceName()
    {
        return 'session';
    }
}