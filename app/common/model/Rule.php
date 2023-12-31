<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-30 11:46:30
 * @LastEditTime: 2022-09-25 22:36:47
 * @FilePath: \web\app\common\model\Rule.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

namespace app\common\model;

use think\Model;


class Rule extends Model
{
    //创建节点菜单
    public static function createNode($data)
    {
        //内容模型不用节点
        if ($data['type'] == 'classic') {
            return;
        }
        $name = ucfirst($data['name']);
        Rule::insertGetId([
            'pid' => $data['rule_id'],
            'name' => $name,
            'path' => '/model',
            'title' => $data['alias'],
            'sort' => $data['sort'],
            'icon' => $data['icon'],
            'status' => 1,
            'mod_id' => $data['id']
        ]);
    }
    //删除节点菜单
    public static function removeNode($data)
    {
        Rule::where('mod_id', $data['id'])->delete();
    }
    public function mod()
    {
        return $this->belongsTo(Mod::class);
    }
    //父目录
    public function setPathAttr($value,$data)
    {
        if ($data['type'] == 1) {
            return '/dir';
        } else {
            return $value;
        }
    }

    public function setStatusAttr($value)
    {
        if ($value == 'true') {
            return 1;
        } else {
            return 0;
        }
    }
}
