<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-07-14 07:38:08
 * @LastEditTime: 2022-08-24 07:43:03
 * @FilePath: \web\app\admin\controller\classify.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

declare(strict_types=1);

namespace app\admin\controller;

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
        return $dataList;
    }
    public function getSelect($pid = 0, $ids = '')
    {
        if ($pid) {
            return $this->table->getSelected($pid, $ids);

            $data['mod'] = 'classify';
            $this->success('获取栏目树成功', $data);
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
        # code...
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
