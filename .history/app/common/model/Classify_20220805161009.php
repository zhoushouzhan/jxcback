<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-07-14 07:34:28
 * @LastEditTime: 2022-08-05 16:06:33
 * @FilePath: \web\app\common\model\Classify.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

declare(strict_types=1);

namespace app\common\model;

use think\facade\Db;
use think\Model;

class Classify extends Model
{
    //写入后动作
    public static function onAfterInsert($data)
    {
        $pid = $data->pid;
        if ($pid > 0) {
            $parent = self::find($pid);
            $parent->havesid = 1;
            $data->path = $parent->path . $pid . ',';
            $parent->save();
        } else {
            $data->path = '0,';
        }
        $data->save();
    }
    //删除后动作
    public static function onAfterDelete($res)
    {
        $data = self::select();
        $sonids = self::getSon($data, $res->id);
        if ($sonids) {
            Db::name('classify')->delete($sonids);
        }
        //是否还有同级
        if (!$count = Db::name('classify')->where('pid', $res->pid)->count()) {
            Db::name('classify')->where('id', $res->pid)->update(['havesid' => 0]);
        }
    }
    //获取所有子类
    static private function getSon($data, $id)
    {
        static $ids = [];
        foreach ($data as $k => $v) {
            if ($v->pid == $id) {
                $ids[] = $v->id;
                self::getSon($data, $v->id);
            }
        }
        return $ids;
    }
    //下拉菜单
    public static function getSelected($pid, $ids)
    {
        $data = self::whereRaw("FIND_IN_SET($pid,path)")->select();
        $ids = explode(',', $pid.','.$ids);
        $arr=[];
        foreach ($ids as $id) {
            foreach($data as $k=>$v){
                
            }
        }

        return $data;
    }
    //复选框
    public static function getCheckbox($ids, $pid)
    {
        $item = self::where('pid', $pid)->select();
        $checkbox = '';
        $idsArr = [];
        $ids = (string) $ids;
        if ($ids) {
            $idsArr = explode(',', $ids);
        }

        foreach ($item as $key => $value) {
            $value->current = 0;
            if (in_array($value->id, $idsArr)) {
                $value->current = 1;
            }
            $newItem[] = $value;
        }
        return $newItem;
    }
    //单选框
    public static function getRadio($id, $pid)
    {
        $item = self::where('pid', $pid)->select();
        foreach ($item as $key => $value) {
            $value->current = 0;
            if ($value->id == $id) {
                $value->current = 1;
            }
            $newItem[] = $value;
        }
        return $newItem;
    }
}
