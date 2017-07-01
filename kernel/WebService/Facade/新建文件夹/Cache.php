<?php
/**
 * /Web/facade/Cache.php @ TeePot
 *
 * Copyright (C) 2016 Moycat <moycat@makedie.net>
 *
 * This file is part of TeePot.
 *
 * TeePot is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TeePot is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TeePot. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Facade;

use \Redis;

class Cache {
    private static $redis;

    public static function init($host, $port, $password)
    {
        self::$redis = new Redis();
        $rs = self::$redis->pconnect($host, $port);
        if (!$rs) {
            die("Failed to connect to the Redis server.");
        }
        if ($password) {
            $rs = self::$redis->auth($password);
            if (!$rs) {
                die("Failed to connect to the Redis server.");
            }
        }
        Site::debug('Connected to the Redis.');
    }

    public static function __callStatic($name, $arg)
    {
        return call_user_func_array([self::$redis, $name], $arg);
    }
}