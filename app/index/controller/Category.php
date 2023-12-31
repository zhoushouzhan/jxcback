<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-21 09:57:50
 * @LastEditTime: 2022-09-19 09:41:35
 * @FilePath: \web\app\index\controller\Index.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

namespace app\index\controller;

use think\facade\View;

class Category extends Base
{
    public function initialize()
    {
        parent::initialize();

        $this->categoryid = $this->request->param('category_id');
        $this->category = \app\common\model\Category::find($this->categoryid);
        $modid = $this->category->mod_id;
        if (!$modid) {
            $this->error('模型ID错误');
        }
        $this->mod = \app\common\model\Mod::find($modid);
        $this->table = $this->mod->getMod();
    }

    public function index($CategoryId)
    {

        $dataList = $this->table::select();
        View::assign([
            'category'  => $this->category,
            'dataList' => $dataList
        ]);

        return view();
    }
}
