<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-08-03 11:36:52
 * @LastEditTime: 2022-08-16 10:58:47
 * @FilePath: \web\app\admin\controller\ListInfo.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

namespace app\admin\controller;

class ListInfo extends Base
{


    public function initialize()
    {
        parent::initialize();
        $modid = $this->request->param('modid');
        if (!$modid) {
            $this->error('模型ID错误');
        }
        $this->mod = \app\common\model\Mod::find($modid);

        $this->table = $this->mod->getMod();
    }

    public function getList()
    {
        if ($this->mod['type'] == 'form') {
            $data = $this->table::find(1);
        } else {
            $data = $this->table::order('id', 'desc')->select();
        }

        $this->success('获取成功', $data);
    }
    public function save()
    {
        $data = input('data');

        if (isset($data['id']) && $data['id']) {
            if ($r = $this->table::update($data)) {
                $this->success('更新成功', $r);
            }
        } else {
            if ($r = $this->table::create($data)) {
                $this->success('保存成功', $r);
            }
        }
    }
    public function delete($ids)
    {
        if ($this->table::destroy($ids)) {
            $this->success('删除成功');
        }
    }
}
