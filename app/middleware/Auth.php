<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-22 13:33:56
 * @LastEditTime: 2022-06-22 15:23:46
 * @FilePath: \ypcms2.0\app\middleware\Auth.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

declare(strict_types=1);

namespace app\middleware;

use app\BaseController;
use yp\Auth as JTauth;

class Auth extends BaseController
{
    public function handle($request, \Closure $next)
    {
        $token = (string)$request->header('Authorization');
        $request->jwtData = JTauth::decodeToken($token);

        return $next($request);
    }
}
