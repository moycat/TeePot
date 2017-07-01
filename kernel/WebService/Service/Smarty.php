<?php

namespace TeePot\WebService\Service;

use TeePot\WebService\Service\Contract\ViewContract;
use App;

class Smarty extends ViewContract
{
    protected $smarty;

    public static function register()
    {
        App::bindSingleton('view', __CLASS__);
    }
    
    public function __construct()
    {
        $this->smarty = new \Smarty();
        if (env('DEBUG')) {
            $this->smarty->caching = false;
        } else {
            $this->smarty->caching = true;
        }

        $this->smarty->template_dir = __DIR__.'/../View';
        $this->smarty->compile_dir = __DIR__.'/../../../web/tmp';
        $this->smarty->cache_dir = __DIR__.'/../../../web/tmp';
    }

    protected function assign_default_values()
    {
        $default_value = [
            'SITE_SETTING'  =>  env('SITE'),
            'PROCESS_TIME'  =>  timing()
        ];
        foreach ($default_value as $var => $value) {
            $this->smarty->assign($var, $value);
        }
    }

    public function show($template)
    {
        $this->assign_default_values();
        $this->smarty->display($template.'.tpl');
    }

    public function assign($name, $val)
    {
        $this->smarty->assign($name, $val, true);
    }

    public function error404()
    {
        App::header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
        $this->show('404');
    }

    public function getInstance()
    {
        return $this->smarty;
    }
}