<?php

namespace app\work;

use think\facade\Db;
use think\worker\Server;
use Workerman\Lib\Timer;
class Push extends Server
{
    protected $socket = 'websocket://0.0.0.0:2345';
    public function __construct()
    {
        parent::__construct();
        $this->onMessage();

        $this->worker->onWorkerStart = function ($worker) {
            echo "Worker starting...\n";
        };
    }
    /**
     * 收到信息
     * @param $connection
     * @param $data
     */
    public function onMessage()
    {
        $this->worker->onMessage = function ($connection, $data) {
            dump($data);
            $connection->send($data);
        };
    }
}
