<?php
/**
 * /Web/facade/DB.php @ TeePot
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

use MongoDB\Client;
use MongoDB\BSON\Regex;
use Exception;

class DB {
    protected static $client;
    protected static $db;
    protected static $col = [];
    protected static $col_name = '';

    public static function init($conn_str, $database)
    {
        try {
            self::$client = new Client($conn_str);
        } catch(Exception $e) {
            die("Failed to connect to the database.");
        }
        self::$db = self::$client->selectDatabase($database);
        Site::debug('Connected to the database.');
    }

    public static function select($collection)
    {
        self::$col_name = $collection;
        if (isset(self::$col[$collection])) {
            return self::$col[$collection];
        }
        self::$col[$collection] = self::$db->selectCollection($collection);
        return self::$col[$collection];
    }

    public static function autoinc($mark)
    {
        $rs = self::select('autoinc')->findOneAndUpdate(
            [
                'mark' => $mark
            ],
            [
                '$inc' => [
                    'id' => 1
                ]
            ],
            [
                'upsert' => true
            ]
        );
        return $rs->id;
    }

    public static function regex($str, $mode = 'i') {
        return new Regex($str, $mode);
    }

    public static function __callStatic($name, $arg)
    {
        return call_user_func_array([self::$col[self::$col_name], $name], $arg);
    }
}