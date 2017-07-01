{*********************************************************
 * /Web/view/index.tpl @ TeePot
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
{extends file='page.tpl'}
{block name='wrapper'}
    {include file='common/head.tpl' active='home'}
    <div class="row">
        <div class="col-md-12">
            <form role="form" class="index-query">
                <div class="input-group">
                    <input type="search" class="form-control" id="search" name="search" placeholder="I'm Feeling Lucky!" autofocus="autofocus">
                    <span class="input-group-btn">
                        <button class="btn btn-danger" type="button" onclick="query()">
                            <span class="glyphicon glyphicon-search"></span> Go!
                        </button>
                    </span>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 index-intro">
            <p>迄今已有<code>{$lib_count|default:0}</code>组共<code>{$data_count|default:0}</code>条数据收入库中<span class="hidden-500"> ヽ(✿ﾟ▽ﾟ)ノ</span></p>
            <p>具体数据来源及条数请参见<a href="/about">关于</a></p>
        </div>
    </div>
    <div id="query-result" style="display: none">
        <div class="row">
            <div class="col-md-12">
                <div class="alert" role="alert" id="info" style="display: none">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="progress">
                    <div class="progress-bar progress-bar-danger progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0;">
                    </div>
                    <div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0;">
                    </div>
                    <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0;">
                        0%
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="page-header">
                    <h2>
                        查询【<span id="query-str"></span>】
                        <small class="new-line-768">
                            已找到<code id="hits-count">0</code>条
                        </small>
                    </h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div id="query-result-panel" class="col-md-12">
                <div class="panel panel-default">
                </div>
            </div>
        </div>
    </div>
{/block}