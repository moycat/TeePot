<?php
namespace TeePot;

use Workerman\Worker;
use Workerman\Lib\Timer;
use Elasticsearch\ClientBuilder;
use MongoDB\Client;

class ESReader
{
    /**
     * Worker instance.
     * @var Worker
     */
    protected $_worker = null;

    /**
     * Elasticsearch client instance.
     * @var \Elasticsearch\Client
     */
    protected $es = null;

    /**
     * Index of ES.
     * @var string
     */
    protected $es_index = null;

    /**
     * How many results per shard sends back.
     * @var int
     */
    protected $es_scroll_size = 1000;

    /**
     * MongoDB client instance.
     * @var \MongoDB\Client
     */
    protected $mongodb_client = null;

    /**
     * MongoDB collection instance.
     * @var \MongoDB\Collection
     */
    protected $db = null;

    /**
     * Used to share things among processes.
     * @var GlobalDataClient
     */
    protected $cloud = null;

    /**
     * Configurations about query.
     * @var array
     */
    protected $config = [];

    /**
     * List of data.
     * @var array
     */
    protected $dataList = [];

    /**
     * Number of databases.
     * @var int
     */
    protected $libCount = 0;

    /**
     * Number of data.
     * @var int
     */
    protected $dataCount = 0;

    /**
     * Queue of tasks.
     * @var array
     */
    protected $tasks = [];

    /**
     * Number of tasks being processed.
     * @var int
     */
    protected $tasks_in_process = 0;

    /**
     * Interval between two queries from a unique IP/user.
     * @var int
     */
    protected $query_interval = 5;

    /**
     * Last time when a IP/user added a task.
     * @var array
     */
    protected $request_history = [
        'IP'    =>  [],
        'client'=>  []
    ];

    /**
     * Construct.
     *
     * @var string $ip
     * @var int $port
     */
    public function __construct($ip = '0.0.0.0', $port = 8337)
    {
        // Read configurations
        global $config;
        $this->config = $config['QUERY'];
        $this->es_index = $config['ES']['INDEX'];
        $this->es_scroll_size = $config['QUERY']['SCROLL_SIZE'];
        $this->query_interval = $this->config['USER_INTERVAL'];

        // Make up instances
        $this->es = ClientBuilder::create()
            ->setHosts($config['ES']['CONN_STR'])
            ->build();
        $this->mongodb_client = new Client($config['DB']['CONN_STR']);
        $this->db = $this->mongodb_client->selectDatabase($config['DB']['DB_NAME']);
        $this->cloud = new GlobalDataClient('127.0.0.1:2207');
        Timer::add($config['QUERY']['REFRESH'], [$this, 'refreshDataList']);

        // Set up the worker
        $worker = new Worker("text://$ip:$port");
        $worker->name = 'ESReader';
        $worker->reusePort = true;
        $worker->onMessage = [$this, 'onMessage'];
        $worker->onWorkerStart = [$this, 'onWorkerStart'];
        $this->_worker = $worker;
    }

    /**
     * Emit when process start.
     *
     * @var Worker $worker
     * @throws \Exception
     */
    public function onWorkerStart($worker)
    {
        // Delay 2 secs in case the server hasn't started
        Timer::add(2, [$this, 'refreshDataList'], [], false);
    }

    /**
     * Emit when requests coming.
     *
     * @param \Workerman\Connection\TcpConnection $connection
     * @param array $data
     * @return void
     */
    public function onMessage($connection, $data)
    {
        // Try to decode the request
        $data = json_decode($data, true);
        if (!$data || !isset($data['query_str'], $data['client'], $data['ip'])) {
            return;
        }

        // Acquire fields from the request
        $query_str = $data['query_str'];
        $client = $data['client'];
        $ip = $data['ip'];

        // Process the request and return results
        $result = $this->query($query_str, $client, $ip);

        // If a new task added, deal with it now
        if (isset($result['info']) && $result['info'] == 'added') {
            end($this->tasks[md5($query_str)]['futures'])->wait();
        }
    }

    /**
     * Add a task or get the result.
     *
     * @param string $query_str
     * @param string $client
     * @param string $ip
     * @return string
     */
    protected function query($query_str, $client, $ip)
    {
        $hash = md5($query_str);
        if (isset($this->tasks[$hash])) {
            // Requesting an existing task
            return ['info' => 'nochange'];
        } else {
            // Requesting to add a new task
            $interval = $this->config['USER_INTERVAL'];
            $bad = $this->badTask($client, $ip, $interval);
            if ($bad) { // Bad request
                $this->cloud->$hash = [
                    'status'    =>  -1,
                    'info'      =>  $bad['info'],
                    'done'      =>  0,
                    'total'     =>  $this->libCount
                ];
                $this->cloud->expire($hash, $this->config['ALIVE_TIME']);
                return $bad;
            }
            $this->request_history['IP'][$ip] =
            $this->request_history['client'][$client] = time();
            $this->addTask($query_str);
            return ['info' => 'added'];
        }
    }


    /**
     * Add a query task.
     *
     * @param string $query_str
     */
    protected function addTask($query_str)
    {
        $hash = md5($query_str);
        $this->tasks_in_process++;
        $this->tasks[$hash] = null;    // Placeholder
        $task = [
            'query'     =>  $query_str,     // Query string
            'total'     =>  count($this->dataList), // Databases to deal with
            'done'      =>  0,      // Databases dealt with
            'hits'      =>  0,      // Results found
            'begin_time'=>  $this->getTime(),       // Time when this task created
            'end_time'  =>  0,      // Time when this task finished
            'run_time'  =>  0,      // Time used to process this task
            'results'   =>  [],     // Results, grouped by databases
            'futures'   =>  [],     // Future objects of ES
            'timer'     =>  null    // Self-destruct timer
        ];
        $q = [
            'sort'  => ["_doc"],
            'query' => [
                'multi_match' => [
                    'fields' => [],
                    'query' => $query_str,
                    'type' => 'phrase_prefix'   // Just prefix
                ]
            ]
        ];

        // Add promises for each database
        foreach ($this->dataList as $data) {
            // Apply settings of each database
            $q['query']['multi_match']['fields'] = $data['searchable'];
            $query = $this->getQuery($data['name'], $q);

            // Create a promise
            $future = $this->es->search($query);
            $future->then(function ($raw_result) use ($data, $hash) {
                $this->saveResult($raw_result, $hash, $data['source'], $data['fields']);
            }, function () use ($data, $hash) {
                $this->saveResult([], $hash, $data['source'], []);
            });
            $task['futures'][] = $future;
        }

        // Self-destruct in case of out of memory
        $task['timer'] = Timer::add(
            $this->config['ALIVE_TIME'],
            [$this, 'deleteTask'],
            [$hash],
            false
        );

        $this->tasks[$hash] = $task;
    }

    /**
     * Save a result returned by a future.
     *
     * @param $raw_result
     * @param $hash
     * @param $source_name
     * @param $fields
     */
    public function saveResult($raw_result, $hash, $source_name, $fields)
    {
        $this_hits = $raw_result ? $raw_result['hits']['total'] : 0;

        $result = [
            'name'      =>  $source_name, // Hard-code the source name
            'hits'      =>  [],
            'hits_count'=>  $this_hits,
            'fields'    =>  $fields
        ];

        if ($this_hits > 0) {
            // Use scroll & scan to fetch all results
            $scroll_id = $raw_result['_scroll_id'];
            $content = [];
            foreach ($raw_result['hits']['hits'] as $piece) {
                $content[] = $piece['_source'];
            }
            while (true) {
                $response = $this->es->scroll([
                        "scroll_id" => $scroll_id,
                        "scroll"    => "10s"
                    ]
                );

                if (count($response['hits']['hits']) > 0) {
                    $scroll_id = $response['_scroll_id'];
                    foreach ($response['hits']['hits'] as $piece) {
                        $content[] = $piece['_source'];
                    }
                } else {
                    break;
                }
            }

            $result['hits'] = $content;
            $this->tasks[$hash]['hits'] += $this_hits;
        }
        $this->tasks[$hash]['results'][] = $result;

        $done_tasks = ++$this->tasks[$hash]['done'];
        $total_tasks = $this->tasks[$hash]['total'];

        // If all databases returned then the task finishes
        if ($done_tasks >= $total_tasks) {
            $this->tasks_in_process--;
            $now_time = $this->getTime();
            $begin_time = $this->tasks[$hash]['begin_time'];
            $this->tasks[$hash]['end_time'] = $now_time;
            $this->tasks[$hash]['run_time'] = $now_time - $begin_time;
        }

        $overview = [
            'done'  =>  $done_tasks,
            'hits'  =>  $this->tasks[$hash]['hits'],
            'begin' =>  $this->tasks[$hash]['begin_time'],
            'end'   =>  $this->tasks[$hash]['end_time'],
            'run'   =>  $this->tasks[$hash]['run_time'],
            'total' =>  $this->libCount
        ];
        $this->cloud->$hash = $overview;
        $this->cloud->expire($hash, $this->config['ALIVE_TIME']);

        $id = $done_tasks - 1;
        $key = "$hash-$id";
        $result_hits = $result['hits'];
        unset($result['hits']);
        $this->cloud->$key = $result;
        $result_hits = array_chunk($result_hits, 1000);
        $key .= '-';
        foreach ($result_hits as $n => $result_hits_slice) {
            $slice_key = $key.$n;
            $this->cloud->$slice_key = $result_hits_slice;
            $this->cloud->expire($slice_key, $this->config['ALIVE_TIME']);
        }
    }

    /**
     * Delete a existing task when it ages
     *
     * @param $hash
     */
    public function deleteTask($hash)
    {
        $done_tasks = $this->tasks[$hash]['done'];
        $total_tasks = $this->tasks[$hash]['total'];

        // If $this->tasks_in_process hasn't decreased for its finishing
        if ($done_tasks < $total_tasks) {
            $this->tasks_in_process--;
        }

        unset($this->tasks[$hash]);
    }

    /**
     * Check whether a task request is bad
     *
     * @param $client
     * @param $ip
     * @param $interval
     * @return array|bool
     */
    protected function badTask($client, $ip, $interval)
    {
        // Bad request?
        if (!$client || !$ip) {
            return ['info' => 'bad'];
        }

        // IP/user requesting too fast?
        if (isset($this->request_history['IP'][$ip])) {
            if (time() - $this->request_history['IP'][$ip] < $interval) {
                return ['info' => 'fast'];
            }
        }
        if (isset($this->request_history['client'][$client])) {
            if (time() - $this->request_history['client'][$client] < $interval) {
                return ['info' => 'fast'];
            }
        }

        // More than allowed task number?
        if ($this->tasks_in_process >= $this->config['CONCURRENT']) {
            return ['info' => 'busy'];
        }

        // Task is permitted
        return false;
    }

    /**
     * Get current time in ms
     *
     * @return string
     */
    public function getTime()
    {
        list($m, $s) = explode(" ", microtime());
        return sprintf('%f', ($s + $m) * 1000);
    }

    /**
     * Get the newest list of data from database.
     *
     * @return void
     */
    public function refreshDataList()
    {
        $dataList_raw = $this->db->selectCollection('datalist')->find();
        $dataList = [];
        $itemCount = 0;
        foreach ($dataList_raw as $item) {
            $dataList[] = [
                'name'      =>  $item['name'],
                'source'    =>  $item['source'],
                'count'     =>  $item['count'],
                'timestamp' =>  $item['timestamp'],
                'searchable'=>  $item['searchable']->getArrayCopy(),
                'fields'    =>  $item['fields']->getArrayCopy()
            ];
            $itemCount += $item['count'];
        }

        $this->dataList = $dataList;
        $this->dataCount = $itemCount;
        $this->libCount = count($dataList);
        // Save to cloud
        $this->cloud->dataList = $dataList;
        $this->cloud->dataCount = $itemCount;
        $this->cloud->libCount = $this->libCount;
    }

    /**
     * Generate a query
     *
     * @param string|array $type
     * @param array $body
     * @return array
     */
    protected function getQuery($type, Array $body)
    {
        // Multi-type query?
        if (is_array($type)) {
            $type = implode(',', $type);
        }

        return [
            'index' => $this->es_index,
            'type'  => $type,
            'body'  => $body,
            'scroll' => '10s',
            'size' => $this->es_scroll_size,
            'client' => [
                'future' => 'lazy'
            ]
        ];
    }
}