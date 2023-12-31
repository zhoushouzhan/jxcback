<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-21 09:57:50
 * @LastEditTime: 2022-07-31 09:21:47
 * @FilePath: \web\app\admin\controller\mod.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

namespace app\admin\controller;

use app\admin\validate\CheckMod;
use think\exception\ValidateException;
use think\facade\Config;

class Mod extends Base
{
    public function index(String $keyword = '')
    {
        $map = [];

        if ($keyword) {
            $map[] = ['alias|name', 'like', "%$keyword%"];
        }

        $dataList = \app\common\model\Mod::where($map)->order('id', 'desc')->select();
        return $dataList;
    }
    public function getMod($id=0)
    {
        if($id){
            return \app\common\model\Mod::
        }
    }
    //获取模型配置
    public function getparam()
    {
        $param['rule'] = \app\common\model\Rule::where('pid', 0)->order('sort', 'asc')->field('id,name,title')->select();
        $param['modType'] = Config::get('app.modType');
        $param['actions'] = Config::get('app.actions');
        return $param;
    }
    public function save()
    {
        $data = $this->request->param();
        try {
            //验证
            $valCheck = validate(CheckMod::class)->scene('add')->check($data);
            if ($valCheck !== true) {
                $this->error($valCheck);
            }
        } catch (ValidateException $e) {
            $this->error($e->getError());
        }
        if (\app\common\model\Mod::create($data)) {
            $this->success('添加成功');
        } else {
            $this->error('添加失败');
        }
    }
    public function update()
    {
        $data = $this->request->param();
        try {
            //验证
            $valCheck = validate(CheckMod::class)->scene('edit')->check($data);
            if ($valCheck !== true) {
                $this->error($valCheck);
            }
        } catch (ValidateException $e) {
            $this->error($e->getError());
        }
        if (\app\common\model\Mod::update($data)) {

            $this->success('编辑成功');
        } else {
            $this->error('编辑失败');
        }
    }
    public function delete()
    {
        $id = $this->request->param('id/d');
        if (!$id) {
            $this->error('参数错误');
        } else {
            if (\app\common\model\Mod::destroy($id)) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        }
    }
    //增加字段
    public function updateCol(int $id, array $colids = [])
    {
        if ($this->request->isPost()) {
            $r = \app\common\model\Mod::find($id);
            $r->cols = json_encode($colids);
            $r->save();
            $this->success('字段更新成功', $r);
        }
    }
}
