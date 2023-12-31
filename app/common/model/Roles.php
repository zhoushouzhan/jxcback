<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-30 11:46:30
 * @LastEditTime: 2022-10-13 13:21:38
 * @FilePath: \web\app\common\model\Roles.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

namespace app\common\model;

use think\Model;

class Roles extends Model
{
    public function setStatusAttr($value)
    {
        if ($value == 'true') {
            return 1;
        } else {
            return 0;
        }
    }
    public function admin()
    {
        return $this->belongsToMany('Admin', 'Access');
    }

    public function setRulesAttr($value)
    {
        return implode(',', $value);
    }
    public function getRulesAttr($value)
    {
        if ($value) {
            return explode(',', $value);
        }else{
            return [];
        }
    }
    public function setModbtnAttr($value)
    {
        if ($value) {
            return json_encode($value);
        }
    }
    public function getModbtnAttr($value)
    {
        if ($value) {
            return json_decode($value);
        }else{
            return [];
        }
    }
}
