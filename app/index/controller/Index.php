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

use think\facade\View;
use Curl;

class Index extends Base
{
    public function index()
    {
        View::assign("cols", '2.0进行中');
        return view();
    }
    public function test($wd = 'php')
    {
        $url = 'http://google.com/complete/search?output=toolbar&q=' . urlencode($wd);
        $curl = new Curl\Curl();
        $curl->setOpt(CURLOPT_RETURNTRANSFER, TRUE);
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, FALSE);
        $curl->setOpt(CURLOPT_SSL_VERIFYHOST, FALSE);
        $curl->setOpt(CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        $curl->setOpt(CURLOPT_PROXY, "118.26.110.48");
        $curl->setOpt(CURLOPT_PROXYPORT, "8080");
        $curl->get($url);
        $curl->close();
        if ($curl->error) {
            $data = $curl->error_code;
            halt($data);
        } else {
            $xml = simplexml_load_string($curl->response);

            foreach ($xml as $v) {
                $arr[] = $v->suggestion['data'];
            }
            $data = implode(',', $arr);
        }
        halt($data);
    }
}
