<?php
/**
 * /Web/model/Contract/StaticModelTrait.php @ TeePot
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

namespace TeePot\WebService\Model\Contract;

use TeePot\WebService\Facade\DB;

trait StaticModelTrait {

    /*
     * You have to define $collection and $model in the class manually.
     *
     * $collection  string   The name of the collection
     * $model       string   The class of the model
     */

    static protected $member;

    /* Load by ObjectID */
    static public function load($id, $reload = false)
    {
        if (!$reload && isset(self::$member[$id])) {
            return self::$member[$id];
        }
        DB::select(self::getCollectionName());
        self::$member[$id] = DB::select(self::getCollectionName())->
        findOne(['_id' => Site::ObjectID($id)]);
        return self::$member[$id];
    }

    static public function find($filter,  $option = [])
    {
        return DB::select(self::getCollectionName())->findOne($filter, $option);
    }

    static public function findMany($filter, $option = [])
    {
        return DB::select(self::getCollectionName())->find($filter, $option);
    }

    static public function count()
    {
        return DB::select(self::getCollectionName())->count();
    }

    static public function db()
    {
        return DB::select(self::getCollectionName());
    }

    /* Create a new model */
    static public function one()
    {
        return new self;
    }
}