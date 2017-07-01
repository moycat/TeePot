<?php
/**
 * /Web/facade/Request.php @ TeePot
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

class Request {
    static public function post($name)
    {
        if (isset($_POST[$name])) {
            return $_POST[$name];
        }
        return null;
    }

    static public function get($name)
    {
        if (isset($_GET[$name])) {
            return $_GET[$name];
        }
        return null;
    }

    static public function getIP()
    {
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) { // For Cloudflare
            return $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        if (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        return null;
    }

    static public function getAgent()
    {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            return $_SERVER['HTTP_USER_AGENT'];
        }
        return null;
    }
}