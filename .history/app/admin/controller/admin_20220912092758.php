<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-21 09:57:50
 * @LastEditTime: 2022-09-12 09:27:56
 * @FilePath: \web\app\admin\controller\admin.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

namespace app\admin\controller;

use yp\Auth as JTauth;
use app\common\model\Admin as AdminModel;
use think\exception\ValidateException;
use app\admin\validate\CheckLogin;


class Admin extends Base
{
    protected function initialize()
    {
    }
    public function index()
    {
        return '用户首页';
    }
    public function login()
    {
        if ($this->request->isPost()) {
            $data = $this->request->only(['username', 'password', 'verify']);
            try {
                validate(CheckLogin::class)->check($data);
                $data['ip'] = $this->request->ip();
                $admin = AdminModel::login($data);
                if ($admin) {
                    $this->request->token = JTauth::getToken(['adminid' => $admin->id]);
                    $result['id'] = $admin->id;
                    $result['username'] = $admin->username;
                    $result['truename'] = $admin->truename;
                    $result['status'] = $admin->status;
                    $result['last_ip'] = $admin->last_ip;
                    $result['roles'] = '0';
                    //发送路由
                    $this->getMenu();
                    $this->success('登录成功', $result);
                } else {
                    $this->error('登录失败');
                }
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }
        }
    }
    public function loginOut()
    {
    }
}
