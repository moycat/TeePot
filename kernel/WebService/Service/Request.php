<?php

namespace TeePot\WebService\Service;

use TeePot\WebService\Service\Contract\ServiceContract;
use App;

class Request extends ServiceContract
{
    public static function register()
    {
        App::bindSingleton('request', __CLASS__);
    }
    
    public function post($name)
    {
        if (isset($_POST[$name])) {
            return $_POST[$name];
        }
        return null;
    }

    public function get($name)
    {
        if (isset($_GET[$name])) {
            return $_GET[$name];
        }
        return null;
    }

    public function getIP()
    {
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) { // For Cloudflare
            return $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        if (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        return null;
    }

    public function getAgent()
    {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            return $_SERVER['HTTP_USER_AGENT'];
        }
        return null;
    }
}