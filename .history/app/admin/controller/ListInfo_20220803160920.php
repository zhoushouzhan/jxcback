<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-08-03 11:36:52
 * @LastEditTime: 2022-08-03 16:08:26
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
        $mod = \app\common\model\Mod::find($this->request->param('modid'));

        $this->table = $mod->getMod();
    }

    public function getList()
    {
        $data = $this->table::find(1);
        $this->success('获取成功', $data);
    }
    public function add(Type $var = null)
    {
        # code...
    }
}
