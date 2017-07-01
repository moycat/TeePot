<?php
/**
 * /Web/facade/Site.php @ TeePot
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

class Site {

    static public function init()
    {
        DB::init(DB_CONNSTR, DB_NAME);
        Cache::init(REDIS_HOST, REDIS_PORT, REDIS_PWD);
        Auth::check();
    }
    

    static public function ObjectID($str = null)
    {
        return $str ? new \MongoDB\BSON\ObjectID($str) : new \MongoDB\BSON\ObjectID();
    }

    static public function date($time = null)
    {
        if (!$time) {
            return '从未';
        }
        $time = $time === null || $time > time() ? time() : intval($time);
        $t = time() - $time; // Time lag
        if ($t == 0) {
            $text = '刚刚';
        } elseif ($t < 60) {
            $text = $t . '秒前';
        } // Less than a minute
        elseif ($t < 60 * 60) {
            $text = floor($t / 60) . '分钟前';
        } // Less than an hour
        elseif ($t < 60 * 60 * 24) {
            $text = floor($t / (60 * 60)) . '小时前';
        } // Less than an day
        elseif ($t < 60 * 60 * 24 * 3) {
            $text = floor($time / (60 * 60 * 24)) == 1 ? '昨天 ' . date('H:i', $time) :
                '前天 ' . date('H:i', $time);
        } // Less than 3 days
        elseif ($t < 60 * 60 * 24 * 30) {
            $text = date('m月d日 H:i', $time);
        } // Less than a mouth
        elseif ($t < 60 * 60 * 24 * 365) {
            $text = date('m月d日', $time);
        } // Less than a year
        else {
            $text = date('Y年m月d日', $time);
        } // More than a year
        return $text;
    }

    static public function debug($msg)
    {
        if (DEBUG) {
            $process_time = timing();
            echo "<!-- [DEBUG][$process_time]$msg -->\n";
        }
    }
}