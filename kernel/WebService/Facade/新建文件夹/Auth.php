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

use \Model\User;

class Auth
{
    private static $uid = null;
    private static $user = null;

    public static function check()
    {
        if (self::$uid) {
            return self::$uid;
        }
        if (self::session_check() || self::cookie_check()) {
            return self::$uid;
        }
        return false;
    }

    public static function user()
    {
        return self::$user;
    }

    public static function admin()
    {
        return (self::$user && self::$user['role'] == 0);
    }

    /**
     * @param array $info       Filters to find a user
     * @param string $password
     * @param int $forgetmenot  Days before cookies expiring
     * @return bool
     */
    public static function login($info, $password, $forgetmenot = 0)
    {
        $rs = User::find($info);
        if (!$rs) {
            return false;
        }
        if (!password_verify($password, $rs['password'])) {
            return false;
        }
        self::session_start($rs);
        if ($forgetmenot) {
            self::cookie_start($rs, $forgetmenot);
        }
        self::$uid = $rs->getID();
        self::$user = $rs;
        Site::debug('Logged in with the password');
        return true;
    }

    private static function session_start($user)
    {
        Session::set('uid', $user->getID());
        Session::set('mask', $user['mask']);
    }

    private static function cookie_start($user, $day)
    {
        $time = time();
        $ticket_exp = $time + $day * 86400; // When the ticket should expire
        $ticket = sha1($user->getID().$user['mask'].$user['password'].$ticket_exp);
        setcookie("uid", $user->getID(), $ticket_exp);
        setcookie("ticket", $ticket, $ticket_exp);
        setcookie("ticket_exp", $ticket_exp, $ticket_exp);
    }

    private static function session_check()
    {
        $uid = Session::get('uid');
        $mask = Session::get('mask');
        if (!$uid || !$mask) {
            return false;
        }
        $user = User::load($uid);
        if (!$user || $mask !== $user['mask']) {
            return false;
        }
        self::$uid = $uid;
        self::$user = $user;
        Site::debug('Login during a session.');
        return true;
    }

    private static function cookie_check()
    {
        if (!isset($_COOKIE['uid'], $_COOKIE['ticket'], $_COOKIE['ticket_exp'])) {
            return false;
        }
        if ($_COOKIE['ticket_exp'] < time()) { // Ticket has expired!
            return false;
        }
        list($uid, $ticket, $ticket_exp) = [
            $_COOKIE['uid'],
            $_COOKIE['ticket'],
            $_COOKIE['ticket_exp']
        ];
        $user = User::load($uid);
        if (!$user) {
            return false;
        }
        $real_ticket = sha1($user->getID().$user['mask'].$user['password'].$ticket_exp);
        if ($real_ticket !== $ticket) {
            return false;
        }
        self::$uid = $uid;
        self::$user = $user;
        self::session_start($user);
        Site::debug('Login with a cookie.');
        return true;
    }
}