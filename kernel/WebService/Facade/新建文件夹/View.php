<?php
/**
 * /Web/facade/View.php @ TeePot
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

use \Smarty;

class View {
    private static $smarty = null;

    public static function show($tp)
    {
        self::setup();
        // Process time should be computed just before showing
        self::$smarty->assign('process_time', timing());
        self::$smarty->display($tp.'.tpl');

        exit();
    }

    public static function assign($name, $var, $nocache = true)
    {
        self::setup();
        self::$smarty->assign($name, $var, $nocache);
    }

    public static function error404()
    {
        header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
        self::show('404');
    }

    private static function setup()
    {
        if (self::$smarty) {
            return;
        }
        self::$smarty = new Smarty();
        if (DEBUG) {
            self::$smarty->caching = false;
        } else {
            self::$smarty->caching = true;
        }
        self::$smarty->template_dir = VIEW;
        self::$smarty->compile_dir = ROOT.'/tmp';
        self::$smarty->cache_dir = ROOT.'/tmp';

        // Assign default values
        $default_value = [
            'site_name' => SITE_NAME,
        ];
        foreach ($default_value as $var => $value) {
            self::$smarty->assign($var, $value);
        }
    }
}