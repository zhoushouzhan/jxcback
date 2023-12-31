<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-21 09:57:50
 * @LastEditTime: 2022-08-06 08:13:32
 * @FilePath: \web\app\index\controller\Index.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

namespace app\index\controller;

use app\BaseController;
use Nyg\Holiday;
use think\facade\View;
use think\facade\Db;
use yp\Ypdate;

class Index extends BaseController
{
    public function index()
    {
        View::assign("cols", '测试变量');
retuir    }
    public function toimg()
    {
        $data = file_get_contents("php://input");
        $file = $this->app->getRootPath() . 'public/time.png';
        $ret = file_put_contents($file, $data, true);
        return '/time.png';
    }
    public function date()
    {
        $time = Ypdate::getNow();
        return $time;
    }
    public function test()
    {
        $data = ['name' => 'system'];
        app('model', ["data" => $data])->createTable();
    }
}
