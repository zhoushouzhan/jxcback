<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-21 09:57:50
 * @LastEditTime: 2022-08-25 12:05:48
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
use think\facade\Config;

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
                $admin_id = AdminModel::login($data);
                if ($admin_id >= 1) {
                    $this->request->token = JTauth::getToken(['adminid' => $admin_id]);

                    $rule = new Rule($this->app);
                    $resault['menu'] = $rule->index();
                    $param['modType'] = Config::get('app.modType');
                    $param['actions'] = Config::get('app.actions');
                    $param['formitem'] = Config::get('app.formitem');
                    $param['rule']
                    $resault['modconfig'] =$param;


                    $this->success('登录成功', $resault);
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
