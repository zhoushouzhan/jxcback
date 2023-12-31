<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-21 09:57:50
 * @LastEditTime: 2022-10-14 21:21:06
 * @FilePath: \web\app\admin\controller\Roles.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

namespace app\admin\controller;

use app\common\model\Roles as RolesModel;
use app\admin\validate\CheckRoles;
use think\exception\ValidateException;
use think\facade\Cache;

class Roles extends Base
{
    protected function initialize()
    {
        parent::initialize();
    }
    public function index()
    {
        $dataList = RolesModel::with('admin')->select();
        $this->success('', $dataList);
    }

    public function save()
    {
        $data = $this->request->param();
        try {
            //验证
            if (isset($data['id'])) {
                $tips = '更新';
            } else {
                $tips = '增加';
            }
            $valCheck = validate(CheckRoles::class)->check($data);
            if ($valCheck !== true) {
                $this->error($valCheck);
            }
        } catch (ValidateException $e) {
            $this->error($e->getError());
        }
        if (RolesModel::create($data, [], true)) {
            $this->success($tips . '成功');
        } else {
            $this->error($tips . '失败');
        }
    }
    public function authupdate()
    {
        $data = $this->request->param();
        $roles = RolesModel::find($data['id']);

        if (!$roles) {
            $this->error('角色不存在');
        }       
        if (RolesModel::update($data)) {
            $this->success('权限分配成功');
            Cache::clear();
        } else {
            $this->error('权限分配失败');
        };
    }
    public function getroles($id = 0)
    {
        if (!$id) {
            return;
        }
        $roles = RolesModel::with('admin')->find($id);
        $this->success('', $roles);
    }

    public function delete($id)
    {
        if (!$id) {
            return;
        }
        $r = RolesModel::with('admin')->find($id);
        if (count($r->admin) > 0) {
            $this->error('请先删除角色下成员再操作');
        }
        if (RolesModel::destroy($id)) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
}
