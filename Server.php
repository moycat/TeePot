<?php
require_once __DIR__ . '/vendor/autoload.php';

use Workerman\Worker;
use TeePot\GlobalDataServer;
use TeePot\ESReader;
use TeePot\TeePot;

$config = require 'config/config.php';
$message = require 'config/message.php';

$ESReader = new ESReader('127.0.0.1', 8337);
$TeeCloud = new GlobalDataServer('127.0.0.1', 2207);
$TeePot = new TeePot('0.0.0.0', 8812);

Worker::runAll();