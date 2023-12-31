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
use think\facade\Session;
use think\facade\Db;

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
    //删除数据后操作
    public static function onAfterDelete($data)
    {
        //删除所有子栏目
        $category = self::where('path', 'like', '%,' . $data->id . ',%')->select();
        foreach ($category as $key => $value) {
            $value->delete();
        }
    }

    //查询后动作
    public static function onAfterRead($res){
        if(Session::get('category')=='getpathinfo'){
            $ids=$res->path.$res->id;
            $grouptitle=Db::name('category')->where('id','in',$ids)->column('id,title');
            $res['pathinfo']=$grouptitle;
        }
        return $res;
    }


    //下拉菜单
    public static function getSelected($pid, $ids)
    {
        $data = self::whereRaw("FIND_IN_SET($pid,path)")->select();
        if ($ids) {
            array_unshift($ids, $pid);
        } else {
            $ids = [$pid];
        }
        $ids = array_unique($ids);
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


    public function mod()
    {
        return $this->belongsTo(Mod::class);
    }
}
