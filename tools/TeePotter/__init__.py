#!/usr/bin/env python3

"""
" /__init__.py @ TeePotter
"
" Copyright (C) 2016 Moycat <moycat@makedie.net>
"
" This file is part of TeePotter.
"
" TeePotter is free software: you can redistribute it and/or modify
" it under the terms of the GNU General Public License as published by
" the Free Software Foundation, either version 3 of the License, or
" (at your option) any later version.
"
" TeePotter is distributed in the hope that it will be useful,
" but WITHOUT ANY WARRANTY; without even the implied warranty of
" MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
" GNU General Public License for more details.
"
" You should have received a copy of the GNU General Public License
" along with TeePotter. If not, see <http://www.gnu.org/licenses/>.
"""

import time, sys
from collections import deque

try:
    import elasticsearch
except Exception:
    print("请先通过pip安装elasticsearch库。")

try:
    from pymongo import *
except Exception:
    print("请先通过pip安装pymongo库。")

try:
    import configparser
except Exception:
    print("请先通过pip安装configparser库。")


TPindex = 'teepot'  # Name of Index


def inputFor(msg, default):
    val = input(msg)
    if not val:
        return default
    return val


def inputNumberFor(msg, default, minimum = 0):
    val = int(inputFor(msg, default))
    if val < minimum:
        return int(minimum)
    return val


def getConfig(file):
    config = configparser.ConfigParser()
    config.read(file)
    mongodb_connstr = config.get('mongodb', 'connstr')
    es_connstr = config.get('elasticsearch', 'connstr')
    return mongodb_connstr, es_connstr


def confirm():
    con = input('输入Y继续：')
    if con != 'Y' and con != 'y':
        print('配置中止')
        sys.exit(0)


class TeeGenerator:
    def __init__(self, iterator, type_name, interval):
        self.interval = interval
        self.iterator = iterator
        self.raw = {"_index": TPindex, "_type": type_name}
        self.now_record = 0

    def iter(self):
        for next_one in self.iterator:
            self.now_record += 1
            if not self.now_record % self.interval:
                print('读取第%d条数据' % self.now_record)
            self.raw['_source'] = next_one
            yield self.raw


class TeePot:
    def __init__(self):
        self.es = None
        self.mongodb = None
        self.es_URL = None
        self.mongodb_URL = None
        self.generator = None
        self.bulk_size = 5000
        self.max_chunk_bytes = 104857600
        self.thread_count = 4

        self.type_name = 'data_' + str(int(time.time()))
        self.type_source = 'Unknown'
        self.type_searchable = list()
        self.fields = list()

    def bulk(self):
        self.es = elasticsearch.Elasticsearch(self.es_URL,
                                              timeout=60,
                                              max_retries=10,
                                              retry_on_timeout=True)
        self.mongodb = MongoClient(self.mongodb_URL)
        esindex = elasticsearch.client.IndicesClient(self.es)
        esindex.create(index=TPindex, ignore=400)

        true_generator = TeeGenerator(self.generator, self.type_name, self.bulk_size)
        from elasticsearch import helpers
        bulk_helper = elasticsearch.helpers.parallel_bulk(client=self.es,
                                            actions=true_generator.iter(),
                                            thread_count=self.thread_count,
                                            chunk_size=self.bulk_size,
                                            max_chunk_bytes=self.max_chunk_bytes)
        for job in bulk_helper:
            pass

        db = self.mongodb.TeePot
        data_list = db.datalist
        new_data = {'name': self.type_name, 'source': self.type_source,
                    'timestamp': time.time(), 'searchable': self.type_searchable,
                    'count': true_generator.now_record, 'fields': self.fields}
        data_list.insert_one(new_data)
        return true_generator.now_record

    def getBufferSize(self):
        self.bulk_size = inputNumberFor('请输入每批写入读取的条数(默认5000)：', 5000, 100)
        self.max_chunk_bytes = inputNumberFor('请输入每批写入的最大大小(单位MiB，默认100M)：', 100, 1) * 1024 * 1024

    def getSource(self):
        self.type_source = inputFor('请输入此库来源(默认Unknown)：', 'Unknown')

    def getThreadCount(self):
        self.thread_count = inputNumberFor('请输入线程数量(默认4)：', 4, 1)

    def getSearchableFields(self, fields):
        self.fields = fields
        while True:
            self.type_searchable = input('请输入此库可被搜索的字段，以空格隔开(不输入则仅第一个字段)：')
            if not len(self.type_searchable):
                self.type_searchable = [fields[0]]
                print('未输入！默认只可被搜索[%s]' % fields[0])
                break
            else:
                self.type_searchable = self.type_searchable.split(' ')
                if set(self.type_searchable).issubset(set(fields)):
                    break
                print('错误！输入的字段并非全部存在')


if __name__ == '__main__':
    print('TeePotter, Authored by Moycat')