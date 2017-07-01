<?php
namespace TeePot;

use Workerman\Lib\Timer;
use GlobalData\Server;

/**
 * Global data server.
 * Modified by Moycat
 */
class GlobalDataServer extends Server
{
    /**
     * Timers of expiration.
     * @var array
     */
    protected $expiration_timer = [];

    /**
     * GlobalDataServer constructor.
     * @param string $ip
     * @param int $port
     * @param array $defaults
     */
    public function __construct($ip = '0.0.0.0', $port = 2207, $defaults = [])
    {
        parent::__construct($ip, $port);
        $this->_worker->name = "TeeCloud";
        $this->_worker->onMessage = array($this, 'onMessage');
        foreach ($defaults as $key => $default) {
            $this->_dataArray[$key] = $default;
        }
    }

    /**
     * onMessage.
     * @param \Workerman\Connection\TcpConnection $connection
     * @param string $buffer
     * @return mixed
     */
    public function onMessage($connection, $buffer)
    {
        if($buffer === 'ping')
        {
            return;
        }
        $data = unserialize($buffer);
        if(!$buffer || !isset($data['cmd']) || !isset($data['key']))
        {
            return $connection->close(serialize('bad request'));
        }
        $cmd = $data['cmd'];
        $key = $data['key'];
        switch($cmd)
        {
            case 'get':
                if(!isset($this->_dataArray[$key]))
                {
                    return $connection->send('N;');
                }
                return $connection->send(serialize($this->_dataArray[$key]));
                break;
            case 'set':
                $this->_dataArray[$key] = $data['value'];
                $connection->send('b:1;');
                break;
            case 'add':
                if(isset($this->_dataArray[$key]))
                {
                    return $connection->send('b:0;');
                }
                $this->_dataArray[$key] = $data['value'];
                return $connection->send('b:1;');
                break;
            // Added to expire something
            case 'expire':
                $time = (int)$data['value'];
                if (isset($this->expiration_timer[$key])) {
                    Timer::del($this->expiration_timer[$key]);
                }
                $this->expiration_timer[$key] = Timer::add($time,
                    function()use($key)
                    {
                        unset($this->expiration_timer[$key]);
                        unset($this->_dataArray[$key]);
                    });
                $connection->send('b:1;');
                break;
            case 'increment':
                if(!isset($this->_dataArray[$key]))
                {
                    return $connection->send('b:0;');
                }
                if(!is_numeric($this->_dataArray[$key]))
                {
                    $this->_dataArray[$key] = 0;
                }
                $this->_dataArray[$key] = $this->_dataArray[$key]+$data['step'];
                return $connection->send(serialize($this->_dataArray[$key]));
                break;
            case 'cas':
                if(isset($this->_dataArray[$key]) && md5(serialize($this->_dataArray[$key])) === $data['md5'])
                {
                    $this->_dataArray[$key] = $data['value'];
                    return $connection->send('b:1;');
                }
                $connection->send('b:0;');
                break;
            case 'delete':
                unset($this->_dataArray[$key]);
                $connection->send('b:1;');
                break;
            default:
                $connection->close(serialize('bad cmd '. $cmd));
        }
    }

    /**
     * Set a value manually
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        $this->_dataArray[$key] = $value;
    }
}


