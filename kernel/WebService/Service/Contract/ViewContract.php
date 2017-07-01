<?php

namespace TeePot\WebService\Service\Contract;

abstract class ViewContract extends ServiceContract
{
    abstract public function show($template);
    
    abstract public function assign($name, $val);
    
    abstract public function error404();
}