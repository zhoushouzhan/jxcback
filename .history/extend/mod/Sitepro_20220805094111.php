<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-08-04 14:40:11
 * @LastEditTime: 2022-08-05 09:41:10
 * @FilePath: \web\extend\mod\Sitepro.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

namespace mod;

trait Sitepro
{
    //关联结束时间
    public function setEndtimeAttr($value, $data)
    {
        return strtotime($value);
    }
    public function getEndtimeAttr($value, $data)
    {
        return date('Y-m-d h:i:s', $value);
    }
}
