<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-30 11:46:30
 * @LastEditTime: 2022-08-04 21:18:53
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
        Rule::createNode($data);
    }

    //删除后
    public static function onAfterDelete($data)
    {

        //删除模型
        app('model', ["data" => $data])->removeMod();
        //删除模型文件
        app('file', ["data" => $data])->removeMod();
        //删除菜单
        Rule::removeNode($data);
    }
    //更新前
    public static function onBeforeUpdate($data)
    {
        //更新数据表
        $updateTable = app('model', ["data" => $data])->updateTable();
        if ($updateTable['code'] != 1) {
            echo json_encode($updateTable);
            exit;
        }
    }
    //查询后
    public static function onAfterRead($data)
    {
        $data['colsid'] = [];
        $data['cols'] = json_decode($data['cols'], true);
        halt(gettype($data['cols']));
        if (is_array($data['cols'])) {
            $data['colsid'] = array_reduce($data['cols'], 'array_merge', []);
        }
        return $data;
    }
    //写入后
    public static function onAfterWrite($data)
    {
        app('file', ["data" => $data])->createModFile();
    }
    //字段分组
    public function setColgroupAttr($value)
    {
        if (!$value) {
            $value = '基本属性';
        }
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

    //功能
    public function setActionsAttr($value)
    {
        return json_encode($value);
    }
    //返回模型数据
    public function getMod()
    {
        $modName = ucfirst($this->getData()['name']);
        return ("\app\common\model\\$modName");
    }
}
