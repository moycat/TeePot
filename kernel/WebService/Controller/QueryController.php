<?php

namespace TeePot\WebService\Controller;

use App;
use TeePot\WebService\Controller\Contract\ControllerContract;
use TeePot\WebService\Facade\Request;
use TeePot\TeePot;

class QueryController extends ControllerContract
{
    public function query()
    {
        // Message returned is of json
        App::header("Content-Type: application/json; charset=utf-8");

        $query_str = Request::post('query_str');
        $skip = abs((int)(Request::post('skip')));

        if (!$this->checkQuery($query_str)) {  // Wrong format
            $result = ['status' => -1, 'info' => 'format'];
        } else {
            $result = TeePot::launchEye(    // Get results -> Add or Fetch
                $query_str,
                md5(json_encode($_COOKIE)),
                $_SERVER['REMOTE_ADDR'],
                $skip
            );
        }
        echo json_encode($result);
    }

    protected function checkQuery($query_str)
    {
        $query_limit = env('QUERY');
        $length = strlen($query_str);
        if ($length > $query_limit['MAX_LENGTH'] ||
            $length < $query_limit['MIN_LENGTH']) {
            return false;
        }
        return true;
    }
}