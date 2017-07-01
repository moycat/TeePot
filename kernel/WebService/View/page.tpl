{*********************************************************
 * /Web/view/page.tpl @ TeePot
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
<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{block name="title"}{$SITE_SETTING['NAME']}{/block}</title>
        <link href="/css/bootstrap.min.css" rel="stylesheet">
        <link href="/css/flat-ui.min.css" rel="stylesheet">
        <link href="/css/flat/red.css" rel="stylesheet">
        <link href="/css/common.css" rel="stylesheet">
        {block name=extra_header}{/block}
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="/js/html5shiv.min.js"></script>
        <script src="/js/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <a class="gotop" onclick="gotop()" href="#" title="返回顶部">
            <span class="glyphicon glyphicon-arrow-up" aria-hidden="true"></span>
        </a>
        <div class="wrapper">
            <div class="container">
                <div class="row">
                    <div class="card">
                        {block name='wrapper'}{/block}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 footer">
                        <hr>
                        <p><samp>Page processed in {nocache}{$PROCESS_TIME}{/nocache} ms.</samp></p>
                        <p><samp>Powered by <a href="https://github.com/moycat/TeePot" target="_blank">TeePot</a> from Moycat.</samp></p>
                    </div>
                </div>
                {include file='common/userform.tpl'}
            </div>
        </div>
    </body>
    <script src="/js/jquery-3.1.1.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="/js/flat-ui.min.js"></script>
    <script src="/js/icheck.min.js"></script>
    <script src="/js/common.js"></script>
    {block name=extra_foot}{/block}
</html>