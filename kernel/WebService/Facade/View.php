<?php

namespace TeePot\WebService\Facade;

use TeePot\WebService\Facade\Contract\FacadeContract;

/**
 * Class View
 * @package Facade
 * @contract Service\Contract\View
 */
class View extends FacadeContract
{
    protected static function getServiceName()
    {
        return 'view';
    }
}