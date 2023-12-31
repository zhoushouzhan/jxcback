<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-08-04 14:40:11
 * @LastEditTime: 2022-08-04 14:55:30
 * @FilePath: \web\extend\mod\Sitepro.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

namespace mod;

trait Sitepro
{
    public function setEndtimeAttr($value)
    {
        return strtotime($value);
    }
    public function getEndtimeAttr($value)
    {
        return date($value);
    }
}
