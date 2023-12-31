<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-08 07:55:45
 * @LastEditTime: 2022-09-15 18:44:28
 * @FilePath: \web\app\admin\validate\CheckIcon.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

declare(strict_types=1);

namespace app\admin\validate;

use think\Validate;

class CheckIcon extends Validate
{
    protected $rule = [
        'title' => 'require',
        'name' => 'require|unique:icon'
    ];

    protected $message = [
        'title.require' => '请输入图标名称',
        'name.unique' => '图标己存在',
        'sort.number' => '排序只能填写数字',
    ];
    protected $scene = [
        'add'  =>  ['pid', 'title', 'name', 'sort'],
        'edit'  =>  ['pid', 'title',  'sort'],
    ];
}
