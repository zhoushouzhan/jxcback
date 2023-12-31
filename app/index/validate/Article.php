<?php

namespace app\index\validate;

use think\Validate;

class Article extends Validate
{
    //验证规则
    protected $rule = [
        'username' => 'require|length:6,22|unique:member|checkUsername',
        'password' => 'require|length:6,22',
        'confirm_pasword' => 'require|confirm:password',
        'lifetime' => 'require|number',
        'mobile' => 'require|mobile',
        'email' => 'require|email'

    ];
    //规则提示语
    protected $message = [
        'username.require' => '用户名必须填写',
        'username.length' => '用户名长度必须保持在6~22个字符之间',
        'username.unique' => '此用户名不可用',
        'password.require' => '密码必须填写',
        'password.length' => '密码长度必须保持在6~22个字符之间',
        'confirm_pasword.require' => '验证密码必须填写',
        'confirm_pasword.confirm' => '两次密码输入不一致',
        'lifetime.require' => '生命周期必须选择',
        'lifetime.number' => '生命周期必须是数字',
    ];
    //场景
    protected $scene = [
        'register' => ['username', 'password', 'confirm_pasword'],
        'login' => ['username', 'password', 'lifetime'],
        'edit' => ['email', 'mobile'],
    ];

    // 检验用户名是否包含中文
    protected function checkUsername($value, $rule, $data = [])
    {
        if (preg_match('/[\x{4e00}-\x{9fa5}]/u', $value) > 0) {
            return '用户名格式错误';
        } else {
            return true;
        }
    }
}
