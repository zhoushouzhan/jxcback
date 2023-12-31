<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-08 07:55:45
 * @LastEditTime: 2022-07-09 18:30:57
 * @FilePath: \ypcms2.0\app\admin\validate\CheckLogin.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

declare(strict_types=1);

namespace app\admin\validate;

use think\Validate;


class CheckLogin extends Validate
{
    protected $rule = [
        'username' => 'require',
        'password' => 'require',

    ];

    protected $message = [
        'username.require' => '请输入用户名',
        'password.require' => '请输入密码',
    ];
}
