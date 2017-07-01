#!/usr/bin/env python3

"""
" /Tools/csv.py @ TeePot
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

try:
    from colorama import init, Fore, Back
    init(autoreset=True)
except Exception:
    print("请先通过pip安装colorama库。")

msg_welcome = '''
=== TeePot 社工库系统 CSV 格式数据导入工具 ===
[适用格式]
* CSV格式
* UTF-8编码
[文件示例]
nickname,password,email
InTheEnd,2dd22502c3fd8117a3446508c15277a4,214359573@qq.com
miumiu,a859e69daac3aa9d88ef99e6f3123396,315931679@qq.com
LionHeart,2dd22502c3fd8117a3446508c15277a4,214359573@qq.com
'''

msg_confirmation = '''
=== 请确认以下的导入配置 ==='''+Fore.YELLOW+'''
[文件] %(file)s
[分隔符] %(delimiter)s [转义符] %(quotechar)s
[字段] %(fields)s
[来源] %(source)s
[可搜索字段] %(searchable)s
'''

import sys
import TeePotter
import codecs
import csv


class CSVData:
    def __init__(self, file_path, delimiter, quotechar):
        self.file = codecs.open(file_path, 'r', 'utf-8')
        self.csvreader = csv.reader(self.file, delimiter=delimiter, quotechar=quotechar)
        self.fields = self.csvreader.__next__()

    def iter(self):
        for one_line in self.csvreader:
            yield dict(zip(self.fields, one_line))


if __name__ == '__main__':
    print(msg_welcome)
    mongodb_conn, es_conn = TeePotter.getConfig('../config.conf')
    if len(sys.argv) < 2:
        file = input('请输入CSV文件地址：')
    else:
        file = sys.argv[1]

    delimiter = TeePotter.inputFor('请输入分隔符(默认为半角逗号：)', ',')
    quotechar = TeePotter.inputFor('请输入转义符(默认为半角双引号)：', '"')

    potter = TeePotter.TeePot()
    potter.es_URL = es_conn
    potter.mongodb_URL = mongodb_conn
    print('处理中……')
    csvdata = CSVData(file, delimiter, quotechar)
    potter.generator = csvdata.iter()

    potter.getSource()
    print('检测到%d个字段：' % len(csvdata.fields))
    print(csvdata.fields)
    potter.getSearchableFields(csvdata.fields)
    potter.getBufferSize()
    potter.getThreadCount()
    print(msg_confirmation % {'file': file, 'fields': str(csvdata.fields),
                              'delimiter': delimiter, 'quotechar': quotechar,
                              'source': potter.type_source,
                              'searchable': str(potter.type_searchable)})
    TeePotter.confirm()
    print('开始写入……')
    record_count = potter.bulk()
    print('已写入：' + str(record_count))