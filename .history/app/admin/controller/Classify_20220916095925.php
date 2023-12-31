<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-07-14 07:38:08
 * @LastEditTime: 2022-09-16 09:59:10
 * @FilePath: \web\app\admin\controller\Classify.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

declare(strict_types=1);

namespace app\admin\controller;

use app\admin\validate\CheckRule;
use think\exception\ValidateException;

class Classify extends Base
{
    protected function initialize()
    {
        parent::initialize();
        $this->table = new \app\common\model\Classify;
        $this->data = $this->table::select();
    }
    public function index($pid = 0, $ids = '')
    {

        $dataList = $this->table->where('pid', $pid)->select();
        $this->success('', $dataList);
    }
    public function getSelect($pid = 0, $ids = '')
    {
        if ($pid) {
            if ($this->table->getSelected($pid, $ids)) {
                $this->success('获取分类成功', $this->table->getSelected($pid, $ids));
            } else {
                $this->error('无子分类');
            }
        } else {
            $this->error('未指定分类');
        }
    }
    public function save()
    {
        if (input("?post.title")) {

            $titles = input('post.title');

            $pid = input('post.pid/d');
            foreach (explode("\n", $titles) as $key => $value) {
                $arr = explode('|', $value);
                if (empty(trim($arr[0]))) {
                    continue;
                }
                $data[$key]['title'] = trim($arr[0]);
                $data[$key]['sort'] = isset($arr[1]) ? (int) $arr[1] : 0;
                $data[$key]['pid'] =  $pid;
            }
            if ($this->table->saveAll($data)) {
                $this->success('增加成功');
            } else {
                $this->error('增加失败');
            }
        } else {
            $this->error('请输入正确的分类');
        }
    }
    public function update()
    {
        $data = $this->request->param();
        try {
            //验证
            $valCheck = validate(CheckRule::class)->scene('edit')->check($data);
            if ($valCheck !== true) {
                $this->error($valCheck);
            }
        } catch (ValidateException $e) {
            $this->error($e->getError());
        }
        if (\app\common\model\Rule::update($data)) {
            $this->success('更新成功');
        } else {
            $this->error('更新失败');
        }
    }
    public function delete($ids)
    {
        $data = $this->table->select();
        if (is_array($ids)) {
            $ids = array_map('intval', $ids); //数值转为整型
            if (count($ids) == 1) {
                $ids = (int) $ids[0];
            }
        }
        if ($this->table->destroy($ids)) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
}
