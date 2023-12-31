<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-30 11:46:30
 * @LastEditTime: 2022-07-25 10:58:41
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
        app('model', ["data" => $data])->createTable();
    }
    //增加后
    public static function onAfterInsert($data)
    {
        //创建节点菜单
        Rule::createMenu($data);
    }

    //删除后
    public static function onAfterDelete($data)
    {

        //删除模型
        $model = app('model', ["data" => $data])->removeMod();
        //删除模型文件
        $file = app('file', ["data" => $data])->removeMod();
        //删除节点菜单
        self::removeMenu($data);
    }
    //写入后
    public static function onAfterWrite($data)
    {

        app('file', ["data" => $data])->createModFile();

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
