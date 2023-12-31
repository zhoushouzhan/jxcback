<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-08-23 14:10:32
 * @LastEditTime: 2022-08-23 14:23:05
 * @FilePath: \web\app\admin\controller\Category.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

declare(strict_types=1);

namespace app\admin\controller;

class Category extends Base
{
    protected function initialize()
    {
        parent::initialize();
        $this->table = new \app\common\model\Category;
    }
    public function getList($page = 1)
    {

        $dataList = $this->table::order(['id' => 'DESC'])->paginate(30, false, ['page' => $page]);
        $this->success('栏目获取成功', $dataList);
    }
    public function save()
    {
        if ($this->request->isPost()) {
            $data = $this->request->param();

            if ($this->table::create($data)) {
                $this->success('保存成功', (string) url('add', ['pid' => $data['pid']]));
            } else {
                $this->error('保存失败');
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
