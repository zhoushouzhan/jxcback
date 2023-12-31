<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-08 07:55:45
 * @LastEditTime: 2022-09-26 18:36:41
 * @FilePath: \web\app\admin\validate\CheckAdmin.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

declare(strict_types=1);

namespace app\admin\validate;

use think\Validate;

class CheckGoods extends Validate
{
    protected $rule = [
        'title' => 'require',
        'code' => 'require|unique:goods',
    ];

    protected $message = [
        'title.require' => '请输入名称',
        'code.require' => '编码必须输入',
        'code.unique' => '此编码己存在',

    ];
    public function sceneEdit()
    {
        return $this->remove('title', 'unique');
    }
}
