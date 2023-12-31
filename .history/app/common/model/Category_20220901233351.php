<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-08-23 13:59:28
 * @LastEditTime: 2022-09-01 23:33:51
 * @FilePath: \web\app\common\model\Category.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

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
        if ($ids == 0) {
            $selectNode[] = $data;
        } else {
            $ids = explode(',', $pid . ',' . $ids);
            $selectNode = [];
            foreach ($ids as $id) {
                $item = [];
                foreach ($data as $k => $v) {
                    if ($v['pid'] == $id) {
                        $item[] = $v;
                    }
                }
                if ($item) {
                    $selectNode[] = $item;
                }
            }
        }
        return $selectNode;
    }
    public function mod()
    {
        return $this->belongsTo(Mod::class);
    }
}
