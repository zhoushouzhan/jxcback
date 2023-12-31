<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-08-04 14:40:11
 * @LastEditTime: 2022-08-04 21:28:29
 * @FilePath: \web\extend\mod\Sitepro.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

namespace mod;

trait Sitepro
{


    //关联结束时间单图片
    public function getEndtimeAttr($value, $data)
    {
        return \app\common\model\Files::find($value);
    }

    //关联地区下拉
    public function getAreaAttr($value, $data)
    {
        return \app\common\model\Mclass::where('id', $value)->find();
    }
}
