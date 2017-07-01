<?php
/**
 * /Web/model/User.php @ TeePot
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

namespace TeePot\WebService\Model;

use TeePot\WebService\Model\Contract\ModelContract;
use TeePot\WebService\Model\Contract\StaticModelTrait;
use TeePot\WebService\Facade\Request;

class User extends ModelContract {
    use StaticModelTrait;

    protected $_json_item = [
        'username',
        'email',
        'permission'
    ];

    protected $_default_item = [
        'permission'    =>  0,
        'mask'          =>  1
    ];

    static public function getCollectionName()
    {
        return 'users';
    }

    public function refreshCache()
    {
        // TODO: Implement refreshCache() method.
    }

    protected function onZip(&$doc)
    {
        // Hash the password
        if (isset($doc['password']) && !password_get_info($doc['password'])['algo']) {
            $doc['password'] = password_hash($doc['password'], PASSWORD_DEFAULT);
        }
        // For new users
        if (!$this->_loaded) {
            $this->_default_item['reg_ip'] = Request::getIP();
            $this->_default_item['created_at'] = time();
        }
    }
}