<?php
namespace TeePot;

use TeePot\WebService\App;
use Workerman\WebServer;
use Workerman\Lib\Timer;
use Workerman\Connection\AsyncTcpConnection;

class TeePot extends WebServer
{
    /**
     * @var \TeePot\WebService\App
     */
    static public $app = null;

    /**
     * @var \Workerman\Connection\AsyncTcpConnection
     */
    static public $eye = null;

    /**
     * Used to share things among processes.
     * @var GlobalDataClient
     */
    static protected $cloud = null;

    /**
     * TeePot constructor.
     * @param string $ip
     * @param int $port
     */
    public function __construct($ip, $port)
    {
        global $config;
        parent::__construct("http://$ip:$port");
        $this->addRoot('', './web/');
        $this->name = 'TeePot';
        $this->count = $config['APP']['WEB_THREAD'];
        self::$cloud = new GlobalDataClient('127.0.0.1:2207');
        $this->buildEye($config['APP']['READER_URI']);

        class_alias('TeePot\\WebService\\Facade\\App', 'App');
        require_once 'WebService/tool.php';
        self::$app = new App();
        self::$app->registerService();
        require_once __DIR__.'/../config/router.php';
    }

    /**
     * Perform a query.
     *
     * @param string $query_str
     * @param string $client
     * @param string $ip
     * @param int $skip
     * @return array
     */
    static public function launchEye($query_str, $client, $ip, $skip = 0)
    {
        $hash = md5($query_str);
        $task_info = self::$cloud->$hash;
        if ($task_info) {
            $done = $task_info['done'];
            $task_status = isset($task_info['status']) ? $task_info['status'] : 0;
            if ($task_status < 0) {
                return self::buildResult($task_status, $task_info['info'], $task_info);
            }
            if ($skip >= $done) {
                return self::buildResult(0, 'nochange');
            }
            $results = [];
            for ($i = $skip; $i < $done; ++$i) {
                $key = "$hash-$i";
                $result = self::$cloud->$key;
                $j = 0;
                $key .= '-';
                $slices = [];
                while (1) {
                    $slice_key = $key.$j;
                    $slice = self::$cloud->$slice_key;
                    if (is_array($slice)) {
                        $slices[] = $slice;
                        ++$j;
                    } else {
                        break;
                    }
                }
                if (count($slices) && is_array($slices)) {
                    $result['hits'] = array_merge(...$slices);
                }
                $results[] = $result;
            }
            return self::buildResult($done - $skip, 'found', $task_info, $results);
        }
        $task_request = [
            'query_str' =>  $query_str,
            'client'    =>  $client,
            'ip'        =>  $ip
        ];
        if (self::$eye->send(json_encode($task_request)) !== false) {
            return self::buildResult(0, 'added');
        } else {
            return self::buildResult(-1, 'server');
        }
    }

    static public function buildResult($status, $info, $task_info = [], $results = [])
    {
        return array_merge([
            'status'    =>  $status,
            'info'      =>  $info,
            'results'   =>  $results
        ], $task_info);
    }

    /**
     * Make a instance of Eye.
     *
     * @param $uri
     */
    public function buildEye($uri)
    {
        if (self::$eye) {
            return;
        }
        self::$eye = new AsyncTcpConnection("text://$uri");
        self::$eye->onClose = function($connection) {
            $connection->reConnect(1);
        };
    }

    public function onWorkerStart()
    {
        parent::onWorkerStart();

        // Delay 2 secs in case the server hasn't started
        Timer::add(2, [self::$eye, 'connect'], [], false);
    }

    /**
     * @param \Workerman\Connection\TcpConnection $connection
     */
    public function onMessage($connection)
    {
        // Record the request time
        $_SERVER['REQUEST_TIME'] = get_time_ms();
        // Fix the bug when many/no '/'s
        $_SERVER['REQUEST_URI'] = './'.trim($_SERVER['REQUEST_URI'], '/');

        parent::onMessage($connection);
    }
}