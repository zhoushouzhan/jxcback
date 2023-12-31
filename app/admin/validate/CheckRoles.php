<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-08 07:55:45
 * @LastEditTime: 2022-09-25 22:48:49
 * @FilePath: \web\app\admin\validate\CheckRoles.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

declare(strict_types=1);

namespace app\admin\validate;

use think\Validate;

class CheckRoles extends Validate
{
    protected $rule = [
        'title' => 'require|unique:roles',
        'sort' => 'require|number',
    ];

    protected $message = [
        'title.require' => '请输入角色名称',
        'title.unique' => '角色名称己存在',
        'sort.require' => '请输入排序',
        'sort.number' => '排序只能填写数字',
    ];
}
