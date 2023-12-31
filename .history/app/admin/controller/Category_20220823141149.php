<?php

declare(strict_types=1);

namespace app\admin\controller;

class Category extends Base
{
    protected function initialize()
    {
        parent::initialize();
        $this->table = new \app\common\model\Files;
    }
    public function index($cid = 0, $keyword = '', $page = 1, $isuse = 1)
    {
        $map = [];
        if (!empty($keyword)) {
            $map[] = ['name', 'like', "%{$keyword}%"];
        }
        if (empty($isuse)) {
            $map[] = ['ypcms_id', '=', 0];
        }
        $files_list = $this->table->where($map)->order(['id' => 'DESC'])->paginate(30, false, ['page' => $page]);
        $this->success('附件获取成功', $files_list);
    }



    public function del($id, $bind = 0)
    {
        if (!$id) {
            return;
        }
        if ($bind == 1) {
            $r = $this->table->save(
                [
                    'ypcms_id' => '',
                    'ypcms_type' => '',
                ],
                ['id' => $id]
            );
            $msg = "取消成功";
        } else {
            $r = $this->table->destroy($id);
            $msg = "删除成功";
        }
        if ($r) {
            $this->success($msg);
        } else {
            $this->error('操作失败');
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
