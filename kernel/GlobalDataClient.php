<?php
namespace TeePot;

use GlobalData\Client;

/**
 * Global data client.
 * Modified by Moycat
 */
class GlobalDataClient extends Client
{
    public function expire($key, $time)
    {
        $connection = $this->getConnection($key);
        $this->writeToRemote(array(
            'cmd'   => 'expire',
            'key'   => $key,
            'value' => $time,
        ), $connection);
        $this->readFromRemote($connection);
    }
}