<?php

namespace TeePot\WebService\Facade;

use TeePot\WebService\Facade\Contract\FacadeContract;
use TeePot\TeePot;

class App extends FacadeContract
{
    public static function getServiceName()
    {
        return 'app';
    }

    public static function getInstance()
    {
        return TeePot::$app;
    }
}