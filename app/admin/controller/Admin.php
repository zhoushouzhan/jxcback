<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-21 09:57:50
 * @LastEditTime: 2022-10-05 21:12:13
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
use app\admin\validate\CheckAdmin;
use think\facade\Cache;

class Admin extends Base
{
    protected function initialize()
    {
        parent::initialize();
    }
    public function index()
    {
        $map=[];
        $map[]=['maxroles','>=',$this->admin->maxroles];
        $dataList = AdminModel::where($map)->withoutField('password,salt')->with('roles')->select();
        $this->success('', $dataList);
    }
    public function save()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            try {

                //验证
                if (isset($data['id'])) {
                    $tips = '更新';
                    $valCheck = validate(CheckAdmin::class)->scene('edit')->check($data);
                } else {
                    $tips = '增加';

                    $valCheck = validate(CheckAdmin::class)->check($data);
                }

                if ($valCheck !== true) {
                    $this->error($valCheck);
                }
                $data['last_ip'] = $this->request->ip();
                if (isset($data['id'])) {
                    if (isset($data['password'])) {
                        $data['salt'] = makeStr(8);
                        $data['password'] = md5($data['password'] . $data['salt']);
                    }
                    $admin = AdminModel::update($data);
                } else {
                    $data['salt'] = makeStr(8);
                    $data['password'] = md5($data['password'] . $data['salt']);
                    $admin = AdminModel::create($data);
                }
                if ($admin) {
                    $this->success($tips . '成功');
                } else {
                    $this->error($tips . '失败');
                }
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }
        } else {
            $this->error('无效请求');
        }
    }


    public function update(){
        if ($this->request->isPost()) {
            $data = $this->request->param();
            try {
                $valCheck = validate(CheckAdmin::class)->scene('update')->check($data);
                if ($valCheck !== true) {
                    $this->error($valCheck);
                }


                if (isset($data['password'])&&isset($data['confirm_password'])&&trim($data['password'])!='') {
                    $data['salt'] = makeStr(8);
                    $data['password'] = md5($data['password'] . $data['salt']);
                }else{
                    unset($data['password']);
                }
                $admin = AdminModel::update($data);
                $this->success('密码更新成功');
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }
        }
    }

    public function getadmin($id = 0)
    {
        if (!$id) {
            return;
        }
        $admin = AdminModel::withoutField('password,salt')->with('roles')->find($id);
        $this->success('', $admin);
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
                    $result['token'] = JTauth::getToken(['adminid' => $admin->id]);
                    Cache::clear();
                    $this->success('登录成功', $result);
                } else {
                    $this->error('登录失败');
                }
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }
        }
    } //删除管理员
    public function delete($id)
    {
        if ($id == 1) {
            $this->error('默认管理员不可删除');
        }
        $admin = AdminModel::find($id);
        if ($admin->delete()) {
            //同时删除角色关系
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
    public function add(){
        $goods=Cache::get('goods');
        if(!$goods){
            $goods=[];
        }
        $item['title']='sdfsfsf';
        $goods[]=$item;
        Cache::set('goods',$goods);


        return 1;
    }
}
