<?php

$config = [
    /* Settings about this site */
    'SITE'  => [
        'NAME'      =>  'TeePot',
        'CLOSED'    =>  false
    ],
    'APP'  => [
        'WEB_THREAD'=>  20,
        'READER_URI'=>  '127.0.0.1:8337'
    ],
    /* Settings & limits about query */
    'QUERY' => [
        // Maximum number of tasks being processed at a time
        'CONCURRENT'    =>  30,
        // Time interval (sec) between A unique IP/user's two queries
        'USER_INTERVAL' =>  5,
        // Minimum length of the string to query with
        'MIN_LENGTH'    =>  3,
        // Maximum length of the string to query with
        'MAX_LENGTH'    =>  50,
        // Alive time of a query (sec)
        'ALIVE_TIME'    =>  600,
        // Time interval (sec) before refresh the data list
        'REFRESH'       =>  600,
        // Maximum number of results per shard sends back
        'SCROLL_SIZE'   =>  1000
    ],
    /* Settings about the database */
    'DB'    => [
        'CONN_STR'  =>  'mongodb://localhost:27017',
        'DB_NAME'   =>  'TeePot'
    ],
    /* Settings of ElasticSearch */
    'ES'    => [
        'CONN_STR'  =>
            [
                '127.0.0.1:9200'
            ],
        'INDEX'     =>  'teepot'
    ],
    'DEBUG' => true
];

return $config;