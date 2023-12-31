<?php

namespace app\common\model;

use think\Model;

class Category extends Model
{
    use \mod\Category;
    //自定义内容
    //写入后动作
    public static function onAfterInsert($data)
    {
        $pid = $data->pid;
        if ($pid > 0) {
            $parent = self::find($pid);
            $data->path = $parent->path . $pid . ',';
        } else {
            $data->path =  0 . ',';
        }
        $data->save();
    }
    //下拉菜单
    public static function getSelected($pid, $ids, $infoid)
    {
        $map[] = ['id', '<>', $infoid];
        $map[] = ['islast', '=', 0];
        $data = self::whereRaw("FIND_IN_SET($pid,path)")->where($map)->select();

        $ids = $ids == 0 ? [0] : explode(',', $pid . ',' . $ids);
        $selectNode = [];
        foreach ($ids as $id) {
            $item = [];
            foreach ($data as $k => $v) {
                if ($v['pid'] == $id) {
                    $item[] = $v;
                }
            }
            if ($item)
                $selectNode[] = $item;
        }
        return $selectNode;
    }
}
