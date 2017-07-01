<?php
/**
 * /Web/facade/Session.php @ TeePot
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

class Session {
    static public function get($name)
    {
        if (self::has($name)) {
            return $_SESSION[$name];
        }
        return null;
    }

    /* Get it and delete it */
    static public function fetch($name)
    {
        if (self::has($name)) {
            $value = $_SESSION[$name];
            unset($_SESSION[$name]);
            return $value;
        }
        return null;
    }

    static public function set($name, $value)
    {
        if (self::has($name)) {
            $old = $_SESSION[$name];
            $_SESSION[$name] = $value;
            return $old;
        }
        $_SESSION[$name] = $value;
        return $value;
    }

    static public function del($name)
    {
        unset($_SESSION[$name]);
        return true;
    }

    static public function has($name)
    {
        return isset($_SESSION[$name]);
    }

    static public function clear()
    {
        session_destroy();
        session_start();
    }
}