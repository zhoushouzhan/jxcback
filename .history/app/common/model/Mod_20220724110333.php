<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-30 11:46:30
 * @LastEditTime: 2022-07-24 11:03:33
 * @FilePath: \web\app\common\model\Mod.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

namespace app\common\model;

use think\Model;


class Mod extends Model
{
    //增加前
    public static function onBeforeInsert($data)
    {
        //创建数据表
        $model = app('model', ["data" => $data])->createTable();
    }
    //字段分组
    public function setColgroupAttr($value)
    {
        if ($value) {
            $arr = explode("\n", $value);
            return json_encode($arr);
        }
    }
    public function getColgroupAttr($value)
    {
        if ($value) {
            $arr = json_decode($value, true);
            return $arr;
        }
    }
    public function getColspAttr($value)
    {
        if ($value) {
            $arr = json_decode($value, true);
            return $arr;
        }
    }
}
