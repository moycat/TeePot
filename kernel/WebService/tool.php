<?php

use TeePot\TeePot;

/**
 * Return the instance of App
 *
 * @return \TeePot\WebService\App
 */
function app()
{
    return TeePot::$app;
}

/**
 * Read configurations
 *
 * @param $key
 * @return mixed
 */
function env($key)
{
    return TeePot::$app->env($key);
}

/**
 * @param int $n
 * @return string
 */
function timing($n = 2)
{

    return sprintf('%.'.$n.'f', get_time_ms() - $_SERVER['REQUEST_TIME']);
}

/**
 * @return float
 */
function get_time_ms()
{
    list($m, $s) = explode(" ", microtime());
    return ($s + $m) * 1000;
}

/**
 * @param string $seed
 * @return string
 */
function random($seed = '')
{
    return str_replace(
        '/',
        'x',
        password_hash($seed.(string)rand(1,10000), PASSWORD_DEFAULT)
    );
}

/**
 * @param string $time
 * @return string
 */
function date_time($time = null)
{
    if (!$time) {
        return '从未';
    }
    $time = ($time === null || $time > time()) ? time() : intval($time);
    $t = time() - $time; // Time lag
    if ($t == 0) {
        $text = '刚刚';
    } elseif ($t < 60) {
        $text = $t . '秒前';
    } // Less than a minute
    elseif ($t < 60 * 60) {
        $text = floor($t / 60) . '分钟前';
    } // Less than an hour
    elseif ($t < 60 * 60 * 24) {
        $text = floor($t / (60 * 60)) . '小时前';
    } // Less than an day
    elseif ($t < 60 * 60 * 24 * 3) {
        $text = floor($time / (60 * 60 * 24)) == 1 ? '昨天 ' . date('H:i', $time) :
            '前天 ' . date('H:i', $time);
    } // Less than 3 days
    elseif ($t < 60 * 60 * 24 * 30) {
        $text = date('m月d日 H:i', $time);
    } // Less than a mouth
    elseif ($t < 60 * 60 * 24 * 365) {
        $text = date('m月d日', $time);
    } // Less than a year
    else {
        $text = date('Y年m月d日', $time);
    } // More than a year
    return $text;
}

/**
 * Go to a URL
 *
 * @param string $url
 * @param int $code
 * @param string $msg
 */
function go($url, $code = 200, $msg = '')
{
    switch ($code) {
        default:
            App::header($_SERVER['SERVER_PROTOCOL'].' '.$code.' '.$msg);
            break;
        case 200:
            App::header($_SERVER['SERVER_PROTOCOL']." 200 OK");
            break;
        case 301:
            App::header($_SERVER['SERVER_PROTOCOL']." 301 Moved Permanently");
            break;
        case 302:
            App::header($_SERVER['SERVER_PROTOCOL']." 302 Found");
            break;
        case 404:
            App::header($_SERVER['SERVER_PROTOCOL']." 404 Not Found");
            break;
    }
    App::header('location: '.$url);
    \Workerman\Protocols\Http::end();
}