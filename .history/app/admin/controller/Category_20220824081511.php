<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-08-23 14:10:32
 * @LastEditTime: 2022-08-24 08:15:06
 * @FilePath: \web\app\admin\controller\Category.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

declare(strict_types=1);

namespace app\admin\controller;

use think\exception\ValidateException;

class Category extends Base
{
    protected function initialize()
    {
        parent::initialize();
        $this->table = new \app\common\model\Category;
    }
    public function getList($page = 1, $limit = 50)
    {

        $res = $this->table::order('id', 'desc')->paginate($limit, false, ['page' => $page]);
        $data['list'] = $res;
        $this->success('获取成功', $data);
    }
    //选择栏目
    public function getSelect()
    {
        $dataList = $this->table::order('desc', 'asc')->order()->select();
        $data['list'] = layoutCategory($dataList);
        $data['mod'] = 'category';
        $this->success('获取栏目树成功', $data);
    }

    public function save()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            if ($this->table::create($data)) {
                $this->success('保存成功');
            } else {
                $this->error('保存失败');
            }
        }
    }
    //更新栏目
    public function update($id)
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();
            try {
                validate(CheckCategory::class)->check($data);
                $children = $this->table::where('path', 'like', "%,{$id},%")->column('id');
                if (in_array($data['pid'], $children)) {
                    $this->error('不能移动到自己的子分类');
                }
                //父栏目
                if (!isset($data['islast']) && $data['pid'] == 0) {
                    $data['islast'] = 0;
                }
                if ($data['pid'] != 0) {
                    $data['islast'] = 1;
                }

                if ($this->table::update($data) !== false) {
                    $this->success('更新成功');
                } else {
                    $this->error('更新失败');
                }
            } catch (ValidateException $e) {
                $this->error($e->getError());
            }
        }
    }
    public function delete($ids)
    {
        if (!$ids) {
            return 0;
        }
        if (is_array($ids)) {
            $ids = array_map('intval', $ids);
        }
        $rs = $this->table::destroy($ids);
        if ($rs) {
            $this->success("删除成功");
        }
    }
}
