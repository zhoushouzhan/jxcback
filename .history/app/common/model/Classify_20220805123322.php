<?php

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
    public static function getSelected($id, $ids)
    {
        $data = self::whereRaw("")->select();
        $arr = [];
        $pathArr = [];
        $path = '';
        if (!empty($id)) {
            //更新
            foreach ($data as $key => $value) {
                if ($value['id'] == $id) {
                    $path = $value['path'];
                }
            }
            //选择指定节点以后的分类
            $pathArr = explode((string) $pid, $path . $id);
            $pathArr = explode(',', $pid . $pathArr[1]);

            $selectNode = [];
            foreach ($pathArr as $k) {
                if ($k == 0) {
                    continue;
                }
                foreach ($data as $v) {
                    if ($k == $v['pid']) {
                        if (in_array($v['id'], $pathArr)) {
                            $v['current'] = 1;
                        } else {
                            $v['current'] = 0;
                        }
                        $selectNode[$k][] = $v;
                    }
                }
            }
        } else {
            //新增
            foreach ($data as $key => $value) {
                if ($value['pid'] == $pid) {
                    $arr[] = $value;
                }
            }
            $selectNode[] = $arr;
        }
        return $selectNode;
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
