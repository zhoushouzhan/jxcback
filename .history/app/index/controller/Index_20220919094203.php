<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-21 09:57:50
 * @LastEditTime: 2022-09-19 09:41:35
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
use think\facade\Config;
use Curl\Curl;

class Index extends BaseController
{
    public function index()
    {
        View::assign("cols", '2.0进行中');
        return view();
    }
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
    public function config()
    {
        halt(Config)
    }
    public function test()
    {
        $data = ['name' => 'system'];
        app('Allexport')->toxlsx();
    }
    public function fanyi($str = '你好', $to = 'en')
    {
        $url = 'https://translate.google.cn/translate_a/t?';
        $params['text'] = urlencode($str);
        $params['sl'] = 'zh-CN';
        $params['tl'] = $to;
        $params['ie'] = 'UTF-8';
        $params['oe'] = 'UTF-8';
        $params['multires'] = 1;
        $params['otf'] = 1;
        $params['it'] = 'srcd_gms.1378';
        $params['ssel'] = 4;
        $params['tsel'] = 6;
        $params['sc'] = 1;




        $curl = new Curl();
        $curl->setOpt(CURLOPT_RETURNTRANSFER, TRUE);
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, FALSE);
        $res = $curl->get($url, $params);
        halt($res);
    }
}
