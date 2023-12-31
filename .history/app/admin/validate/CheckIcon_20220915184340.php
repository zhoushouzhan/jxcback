<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-08 07:55:45
 * @LastEditTime: 2022-09-15 18:43:23
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
        'name' => 'require|unique:rule',
        'sort' => 'require|number',
    ];

    protected $message = [
        'pid.require' => '请选择上级菜单',
        'title.require' => '请输入菜单名称',
        'name.require' => '请输入控制器方法',
        'name.unique' => '组件名己存在',
        'sort.require' => '请输入排序',
        'sort.number' => '排序只能填写数字',
    ];
    protected $scene = [
        'add'  =>  ['pid', 'title', 'name', 'sort'],
        'edit'  =>  ['pid', 'title',  'sort'],
    ];
}
