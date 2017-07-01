<?php

namespace TeePot\WebService\Service\Contract;

abstract class DBContract extends ServiceContract
{
    abstract public function selectTable($table);
}