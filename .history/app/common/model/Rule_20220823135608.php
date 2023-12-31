<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-30 11:46:30
 * @LastEditTime: 2022-08-23 13:55:59
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

        $name = ucfirst($data['name']);
        Rule::insertGetId([
            'pid' => $data['rule_id'],
            'name' => $name,
            'path' => '/model',
            'component' => 'model/index.vue',
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
        $name = ucfirst($data['name']);
        Rule::where('mod_id', $data['id'])->delete();
    }
    public function mod()
    {
        return $this->belongsTo(Mod::class);
    }
}
