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

class Article extends Base
{
    public function index($id)
    {

        $r = \app\common\model\Article::find($id);
        View::assign([
            'r'  => $r
        ]);

        return view();
    }
}
