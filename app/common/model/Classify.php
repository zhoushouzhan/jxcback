<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-07-14 07:34:28
 * @LastEditTime: 2022-09-01 08:11:25
 * @FilePath: \web\app\common\model\Classify.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

declare(strict_types=1);

namespace app\common\model;
use think\facade\Session;
use think\facade\Db;
use think\Model;

class Classify extends Model
{
    //写入后动作
    public static function onAfterWrite($data)
    {
        $pid = $data->pid;
        $path = '0,';
        if ($pid > 0) {
            $parent = self::find($pid);
            db::name('classify')->where('id',$pid)->update(['havesid'=>1]);
            $path = $parent->path . $pid . ',';
        } 

        db::name('classify')->where('id',$data->id)->update(['path'=>$path]); 
    }
    //删除后动作
    public static function onAfterDelete($res)
    {
        $data = self::select();
        $sonids = self::getSon($data, $res->id);
        if ($sonids) {
            Db::name('classify')->delete($sonids);
        }
        //更新子栏目标识
        if (!$count = Db::name('classify')->where('pid', $res->pid)->count()) {
            Db::name('classify')->where('id', $res->pid)->update(['havesid' => 0]);
        }
    }
    //查询后动作
    public static function onAfterRead($res){
        if(Session::get('classify')=='getpathinfo'){
            $ids=$res->path.$res->id;
            $grouptitle=Db::name('classify')->where('id','in',$ids)->column('id,title');
            $res['pathinfo']=$grouptitle;
        }
        return $res;
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
    //联动下拉菜单
    public static function getSelected($pid, $ids)
    {
        $data = self::whereRaw("FIND_IN_SET($pid,path)")->select();
        if ($ids) {
            array_unshift($ids, $pid);//pid加入数组中
        } else {
            $ids = [$pid];
        }
        $ids = array_unique($ids);//去重
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
