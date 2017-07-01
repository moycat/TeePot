{*********************************************************
 * /Web/view/404.tpl @ TeePot
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
    {include file='common/head.tpl' active=''}
    <div class="row">
        <div class="col-md-12">
            <blockquote>
                <h3>Error 404 - Page Not Found</h3>
                <p>Out, out, brief candle!</p>
                <p>Life's but a walking shadow, a poor player</p>
                <p>That struts and frets his hour upon the stage</p>
                <p>And then is heard no more; it is a tale,</p>
                <p>Told by an idiot, full of sound and fury,</p>
                <p><b>Signifying nothing.</b></p>
                </blockquote>
        </div>
    </div>
{/block}