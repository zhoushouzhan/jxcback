<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-21 09:57:50
 * @LastEditTime: 2022-09-15 19:10:47
 * @FilePath: \web\app\admin\controller\Icon.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

namespace app\admin\controller;

use app\admin\validate\CheckIcon;
use think\exception\ValidateException;

class Icon extends Base
{
    public function index(String $keyword = '')
    {
        $map = [];

        if ($keyword) {
            $map[] = ['title', 'like', "%$keyword%"];
        }

        $dataList = \app\common\model\Icon::where($map)->order('id', 'asc')->order('id', 'desc')->select();
        $this->success('获取成功', $dataList);
    }
    public function save()
    {
        $data = $this->request->param();
        try {
            //验证
            $valCheck = validate(CheckCols::class)->scene('add')->check($data);
            if ($valCheck !== true) {
                $this->error($valCheck);
            }
        } catch (ValidateException $e) {
            $this->error($e->getError());
        }
        if (\app\common\model\Cols::create($data)) {
            $this->success('添加成功');
        } else {
            $this->error('添加失败');
        }
    }
    public function edit()
    {
        $data = $this->request->param();
        try {
            //验证
            $valCheck = validate(CheckCols::class)->scene('edit')->check($data);
            if ($valCheck !== true) {
                $this->error($valCheck);
            }
        } catch (ValidateException $e) {
            $this->error($e->getError());
        }
        if (\app\common\model\Cols::update($data)) {

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
            if (\app\common\model\Cols::destroy($id)) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        }
    }
}
