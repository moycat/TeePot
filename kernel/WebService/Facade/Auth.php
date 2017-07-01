<?php

namespace TeePot\WebService\Facade;

use TeePot\WebService\Facade\Contract\FacadeContract;

/**
 * Class Auth
 * @package Facade
 * @contract Service\Contract\Auth
 */
class Auth extends FacadeContract
{
    protected static function getServiceName()
    {
        return 'auth';
    }
}