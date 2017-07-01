<?php

namespace TeePot\WebService;

use Exception;
use NoahBuscher\Macaw\Macaw as Router;
use Workerman\Protocols\Http;

class App
{
    protected $environments;

    protected $services;
    protected $flipped_services;
    protected $singleton_services = [];
    protected $callable_services = [];

    protected $container = [];

    public function __construct()
    {
        global $config, $message;
        $this->environments = $config;
        if ($this->environments['SITE']['CLOSED']) {
            echo $message['SITE_CLOSED'];
            $this->run = function(){return;};
        }
    }

    public function make($service_name, $args = [])
    {
        if (!isset($this->services[$service_name])) {
            throw new Exception('No Service ['.$service_name.'] Registered');
        }

        // Is it callable?
        if (isset($this->callable_services[$service_name])) {
            return $this->callable_services[$service_name](...$args);
        }

        // Is it singleton?
        if (isset($this->singleton_services[$service_name])) {
            return isset($this->container[$service_name]) ?
                $this->container[$service_name] :
                $this->container[$service_name] = $this->compose($service_name, $args);
        } else {
            return $this->compose($service_name, $args);
        }
    }

    protected function compose($service_name, $args)
    {
        $class_name = $this->services[$service_name];
        $reflection = new \ReflectionClass($class_name);
        $constructor = $reflection->getConstructor();

        // Easy if no params needed
        if (is_null($constructor) || !$constructor->getNumberOfParameters()) {
            return new $class_name;
        }

        // Provide all params automatically
        $init_params = array();
        foreach ($constructor->getParameters() as $parameter) {
            $dependency_class = $parameter->getClass();
            if (is_null($dependency_class)) {
                $init_params[] = $this->getNonClassParam($parameter, $args);
            } else {
                $init_params[] = $this->getClassParam($dependency_class->name, $args);
            }
        }
        return $reflection->newInstanceArgs($init_params);
    }

    protected function getClassParam($class_name, $args)
    {
        if (!isset($this->flipped_services[$class_name])) {
            throw new Exception('Dependency Not Satisfied For Service');
        }
        $service_name = $this->flipped_services[$class_name];
        return isset($args[$service_name]) ?
            $this->make($service_name, $args[$service_name]) :
            $this->make($service_name);
    }

    protected function getNonClassParam($parameter, $args)
    {
        if(!$parameter->isDefaultValueAvailable()) {
            $param_name = $parameter->getName();
            if (isset($args[$param_name])) {
                return $args[$param_name];
            }
            throw new Exception('Unable to Construct Service');
        }
        return $parameter->getDefaultValue();
    }

    public function bind($service_name, $service)
    {
        if (isset($this->services[$service_name])) {
            throw new Exception('Service ['.$service_name.'] Already Exists');
        }

        $this->services[$service_name] = $service;
        if (is_callable($service)) {
            $this->callable_services[$service_name] = true;
        } else {
            $this->flipped_services[$service] = $service_name;
        }
    }
    
    public function bindSingleton($service_name, $class_name)
    {
        $this->bind($service_name, $class_name);
        $this->singleton_services[$service_name] = true;
    }

    public function unbind($service_name)
    {
        unset(
            $this->services[$service_name],
            $this->container[$service_name],
            $this->callable_services[$service_name],
            $this->singleton_services[$service_name],
            $this->flipped_services[$service_name]
        );
    }

    public function put($service_name, $instance)
    {
        if (isset($this->services[$service_name])) {
            throw new Exception('Service Name ['.$service_name.'] Already Taken');
        }

        $this->services[$service_name] = $instance;
        if (is_callable($instance)) {
            $this->callable_services[$service_name] = true;
        } else {
            $this->container[$service_name] = $instance;
        }
    }
    
    public function registerService()
    {
        $service_list = require 'service.php';
        foreach ($service_list as $service) {
            $service::register($this);
        }
        $this->flipped_services = array_flip($this->services);
    }

    public function run()
    {
        Router::dispatch();
    }

    public function env()
    {
        $args = func_get_args();
        $env = $this->environments;
        foreach ($args as $arg) {
            if (isset($env[$arg])) {
                $env = $env[$arg];
            } else {
                return null;
            }
        }
        return $env;
    }

    public function header($content, $replace = true, $http_response_code = 0)
    {
        return Http::header($content, $replace, $http_response_code);
    }

    public function end($msg = '')
    {
        Http::end($msg);
    }

    public function setcookie(
        $name,
        $value = '',
        $maxage = 0,
        $path = '',
        $domain = '',
        $secure = false,
        $HTTPOnly = false
    )
    {
        return Http::setcookie($name, $value, $maxage, $path, $domain, $secure, $HTTPOnly);
    }

    public function session_start()
    {
        return Http::sessionStart();
    }
    
}