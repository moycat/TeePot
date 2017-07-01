<?php
/**
 * /Web/controller/IndexController.php @ TeePot
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

namespace TeePot\WebService\Controller;

use \TeePot\WebService\Controller\Contract\ControllerContract;
use \TeePot\WebService\Facade\View;
use \TeePot\WebService\Facade\DB;
use \TeePot\WebService\Facade\Cache;

class IndexController extends ControllerContract
{
    public function home()
    {
        View::assign('data_count', $this->getDataCount());
        View::assign('lib_count', $this->getLibCount());
        View::show('index');
    }

    public function about()
    {
        View::assign('data_list', $this->getDataList());
        View::show('about');
    }

    public function getDataCount()
    {
        return Cache::get('dataCount');
    }

    public function getLibCount()
    {
        return Cache::get('libCount');
    }

    public function getDataList()
    {
        return Cache::getArray('dataList');
    }
}