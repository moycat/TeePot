<?php

namespace Facade;

use Facade\Contract\FacadeContract;

class App extends FacadeContract
{
    protected static function getClassName() {
        return 'Kernel\App';
    }
}