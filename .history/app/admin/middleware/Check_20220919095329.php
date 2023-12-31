<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-21 10:52:00
 * @LastEditTime: 2022-09-19 09:53:29
 * @FilePath: \web\app\admin\middleware\Check.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

namespace app\admin\middleware;

use yp\Auth as JTauth;

class Check
{
    public function handle($request, \Closure $next)
    {
        if ($request->url() != '/admin.php/admin/login') {
            $token = (string)$request->header('Authorization');
            if (!empty($token)) {
                $jwtData = JTauth::decodeToken($token);
                if ($jwtData['status'] != 1001) {
                    $res = ['code' => 0, 'msg' => $jwtData['data'], 'data' => '', 'token' => '', 'url' => '/login'];
                    die(json_encode($res));
                }
                $request->adminid = $jwtData['data']->adminid;
                if (isset($jwtData['token'])) {
                    $request->token = $jwtData['token'];
                }
            } else {
                $res = ['code' => -1, 'msg' => '您还没登录', 'data' => '', 'url' => '/login'];
                die(json_encode($res));
            }
        }
        $response = $next($request);
        return $response;
    }
    public function end(\think\Response $response)
    {
        // 回调行为
    }
}
