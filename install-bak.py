#!/usr/bin/env python3

"""
" /install.py @ TeePot
"
" Copyright (C) 2016 Moycat <moycat@makedie.net>
"
" This file is part of TeePot.
"
" TeePot is free software: you can redistribute it and/or modify
" it under the terms of the GNU General Public License as published by
" the Free Software Foundation, either version 3 of the License, or
" (at your option) any later version.
"
" TeePot is distributed in the hope that it will be useful,
" but WITHOUT ANY WARRANTY; without even the implied warranty of
" MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
" GNU General Public License for more details.
"
" You should have received a copy of the GNU General Public License
" along with TeePot. If not, see <http://www.gnu.org/licenses/>.
"""

import os
import sys

try:
    import configparser
except Exception:
    print("请先通过pip安装ConfigParser库。")

try:
    from colorama import init, Fore, Back
    init(autoreset=True)
except Exception:
    print("请先通过pip安装colorama库。")

msg_welcome = '''
欢迎使用TeePot社工库系统。此向导仅供生成PHP配置文件。
这是一个按GPLv3.0许可证开源的社工库系统，采用了比较奇怪的架构。
请参见GitHub上本项目的仓库：https://github.com/moycat/TeePot

'''+Fore.YELLOW+'''[推荐配置]
* Debian 8
* Python 3
* Nginx + PHP 7
* Elasticsearch 5
* Mongodb 3
* Redis 3'''+Fore.RESET+'''
请确保上述组件可用。不保证其他配置下的正常运行。

'''

msg_end = '''
配置完成！'''+Fore.YELLOW+'''
[配置文件] Web/config/config.php  config.conf
[导库工具] Tools/*.py
[注意事项]
* 请自己配置Web目录为网站根目录
* 请确保URL重定向正常，nginx参考配置文件为Docs/nginx.conf
* 请确保PHP进程用户对Web/tmp目录可写
* 如果没有你想要的导库脚本，请参考已有的自主开发
'''

msg_confirmation = '''
请确认你输入的配置正确：'''+Fore.YELLOW+'''
[站点名]%(Site_name)s
[MongoDB连接字符串]%(MongoDB_conn)s
[Elasticsearch连接字符串]%(Elasticsearch_conn)s
[Redis服务器]%(Redis_host)s
[Redis端口]%(Redis_port)d
[Redis密码]%(Redis_pwd)s
'''

conf_file = '''<?php
/**
 * /Web/config/config.php @ TeePot
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

/* Site Settings */
define('SITE_NAME', '%(Site_name)s');
define('SITE_CLOSED', false);

/* Database Settings */
define('DB_CONNSTR', '%(MongoDB_conn)s');
define('DB_NAME', 'TeePot');

/* Redis Settings */
define('REDIS_HOST', '%(Redis_host)s');
define('REDIS_PORT', %(Redis_port)d);
define('REDIS_PWD', '%(Redis_pwd)s');

/* Elasticsearch Settings */
define('ES_CONNSTR', '%(Elasticsearch_conn)s');
define('ES_INDEX', 'teepot');

/* Debug */
define('DEBUG', false);'''


def inputFor(msg, default):
    val = input(msg)
    if not val:
        return default
    return val


if __name__ == '__main__':

    if not os.access('.', os.W_OK) or not os.access('./Web/config', os.W_OK):
        print('没有对当前目录或Web/config目录的写入权限！请检查后再试')
        sys.exit(1)

    print(msg_welcome)

    config = dict()

    # 站点设置
    print('[站点配置]')
    config['Site_name'] = inputFor('站点名称(默认TeePot)：', 'TeePot')
    print('')

    # Mongodb设置
    print('[MongoDB服务器配置]')
    config['MongoDB_conn'] = inputFor('MongoDB服务器连接字符串(默认mongodb://localhost:27017)：', 'mongodb://localhost:27017')
    print('')

    # Elasticsearch设置
    print('[Elasticsearch配置]')
    config['Elasticsearch_conn'] = inputFor('Elasticsearch服务器连接字符串(默认http://localhost:9200/)：', 'http://localhost:9200/')
    print('')

    # Redis设置
    print('[Redis服务器配置]')
    config['Redis_host'] = inputFor('Redis服务器地址(默认localhost)：', 'localhost')
    config['Redis_port'] = int(inputFor('Redis服务器端口(默认6379)：', 6379))
    config['Redis_pwd'] = input('Redis密码(默认为空)：')
    print('')

    print(msg_confirmation % config)
    con = input('输入Y继续：')
    if con != 'Y' and con != 'y':
        print('配置中止')
        sys.exit(0)

    conf_file = conf_file % config

    file = open('Web/config/config.php', 'w')
    file.write(conf_file)
    cffile = configparser.ConfigParser()
    cffile.add_section('site')
    cffile.add_section('mongodb')
    cffile.add_section('redis')
    cffile.add_section('elasticsearch')
    cffile.set('site', 'name', config['Site_name'])
    cffile.set('mongodb', 'connstr', config['MongoDB_conn'])
    cffile.set('redis', 'host', config['Redis_host'])
    cffile.set('redis', 'port', str(config['Redis_port']))
    cffile.set('redis', 'password', config['Redis_pwd'])
    cffile.set('elasticsearch', 'connstr', config['Elasticsearch_conn'])
    cffile.write(open('config.conf', 'w'))

    print(msg_end)