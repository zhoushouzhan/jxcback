<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-08-14 10:58:21
 * @LastEditTime: 2022-08-18 21:03:00
 * @FilePath: \web\extend\mod\Article.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

namespace mod;

trait Article
{
    public function setUpdateTimeAttr($value)
    {
        return strtotime($value);
    }
}
