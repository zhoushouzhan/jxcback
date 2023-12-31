<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-07-07 16:41:26
 * @LastEditTime: 2022-08-11 14:44:11
 * @FilePath: \web\app\work\Push.php
 * @Description: 
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved. 
 */

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
