{*********************************************************
 * /Web/view/common/head.tpl @ TeePot
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
**********************************************************}
<div class="row">
    <div class="col-md-6">
        <h1>
            {$SITE_SETTING['NAME']}
            <small class="new-line-400">社工库</small>
        </h1>
    </div>
    <div class="col-md-6 head-nav">
        <ul class="nav nav-pills" role="tablist">
            <li role="presentation"{if $active eq 'home'} class="active"{/if}>
                <a href="/">首页</a>
            </li>
            <li role="presentation"{if $active eq 'about'} class="active"{/if}>
                <a href="/about">关于</a>
            </li>
            <li role="presentation"><a onclick="$('#register-modal').modal('show')">注册</a></li>
            <li role="presentation"><a onclick="$('#login-modal').modal('show')">登录</a></li>
        </ul>
    </div>
</div>