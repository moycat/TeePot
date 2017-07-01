<?php
/**
 * /Web/config/router.php @ TeePot
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

use NoahBuscher\Macaw\Macaw as Router;
use TeePot\WebService\Facade\View;

Router::get('/', '\\TeePot\\WebService\\Controller\\IndexController@home');
Router::get('/about', '\\TeePot\\WebService\\Controller\\IndexController@about');

Router::post('/query', '\\TeePot\\WebService\\Controller\\QueryController@query');
Router::post('/register', '\\TeePot\\WebService\\Controller\\UserController@register');
Router::post('/login', '\\TeePot\\WebService\\Controller\\UserController@login');

Router::get('/query/(:any)', '\\TeePot\\WebService\\Controller\\QueryController@query');

Router::error(
    function() {
        View::error404();
    }
);