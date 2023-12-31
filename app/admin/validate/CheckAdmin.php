<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-08 07:55:45
 * @LastEditTime: 2022-09-26 18:36:41
 * @FilePath: \web\app\admin\validate\CheckAdmin.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

declare(strict_types=1);

namespace app\admin\validate;

use think\Validate;

class CheckAdmin extends Validate
{
    protected $rule = [
        'username' => 'require|unique:admin|length:5,15',
        'password' => 'require|confirm:confirm_password|length:6,18',
        'confirm_password' => 'require|confirm:password|length:6,18',
        'truename' => 'max:32',
        'mobile' => 'max:32',
        'status' => 'require',
        'roles_id' => 'require',
    ];

    protected $message = [
        'username.require' => '请输入账号',
        'username.unique' => '账号已存在',
        'username.length' => '账号长度只能在6-18位之间',
        'password.require' => '密码必须填写',
        'password.confirm' => '两次输入密码不一致',
        'confirm_password.confirm' => '两次输入密码不一致',
        'status.require' => '请选择状态',
        'roles_id.require' => '请选择角色',
    ];
    public function sceneEdit()
    {
        return $this->remove('password', 'require')->remove('confirm_password', 'require');
    }
    public function sceneUpdate()
    {
        return $this->remove('password', 'require')
        ->remove('username', 'require')
        ->remove('confirm_password', 'require')
        ->remove('status', 'require')
        ->remove('roles_id', 'require');
    }



}
