{*********************************************************
 * /Web/view/about.tpl @ TeePot
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
    {include file='common/head.tpl' active='about'}
    <div class="row">
        <div class="col-md-12">
            <h3>关于本社工库</h3>
            <blockquote>
                <p>你可以在模板中编辑这里~</p>
            </blockquote>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h3>数据列表</h3>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>来源</th>
                        <th>资料条数</th>
                        <th>收录时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    {foreach $data_list as $item}
                    <tr>
                        <td>{$item@iteration}</td>
                        <td>{$item['source']}</td>
                        <td><code>{$item['count']}</code></td>
                        <td><samp>{date_time($item['timestamp'])}</samp></td>
                    </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
{/block}