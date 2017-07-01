<?php

namespace TeePot\WebService\Service;

use TeePot\WebService\Service\Contract\SessionContract;
use App;

class Session extends SessionContract
{
    public static function register()
    {
        App::bindSingleton('session', __CLASS__);
    }
    
    public function __construct()
    {
        App::session_start();
    }

    public function has($name)
    {
        return isset($_SESSION[$name]);
    }

    public function get($name)
    {
        if ($this->has($name)) {
            return $_SESSION[$name];
        }
        return null;
    }
    
    public function set($name, $val)
    {
        $rt = $this->has($name) ? $this->get($name) : null;
        $_SESSION[$name] = $val;
        return $rt;
    }
    
    public function fetch($name)
    {
        $rt = $this->get($name);
        $this->del($name);
        return $rt;
    }
    
    public function del($name)
    {
        unset($_SESSION[$name]);
    }
    
    public function clear()
    {
        App::session_start();
        $_SESSION = [];
    }
}