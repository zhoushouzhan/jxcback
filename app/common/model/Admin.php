<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-09-18 20:56:03
 * @LastEditTime: 2022-09-26 20:53:02
 * @FilePath: \web\app\common\model\Admin.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

declare(strict_types=1);

namespace app\common\model;

use app\common\model\Roles;
use think\Model;
use think\facade\Db;
class Admin extends Model
{

    public static function onBeforeInsert($data)
    {
    }
    public static function onAfterInsert($data)
    {
    }
    public static function onBeforeUpdate($data)
    {
    }

    //数据写入后事件
    public static function onAfterWrite($data)
    {
        if ($data['roles_id']) {
            //删除角色
            $data->roles()->detach();
            //增加角色
            $data->roles()->saveAll($data['roles_id']);
            $maxrolesid=min($data['roles_id']);
            db::name('admin')->where('id',$data['id'])->update(['maxroles'=>$maxrolesid]);
        }
    }

    //数据删除后事件
    public static function onAfterDelete($data)
    {
        //删除角色
        $data->roles()->detach();
    }
    public function setPasswordAttr($value)
    {
        return $value;
    }
    public static function login($data)
    {

        $where[] = ['username', '=', $data['username']];
        $where[] = ['status', '=', 1];
        if ($admin = self::where($where)->find()) {

            if ($admin->password != md5($data['password'] . $admin->salt)) {
                return 0;
            } else {
                $admin->last_ip = $data['ip'];
                $admin->save();
                return $admin;
            }
        } else {
            return 0;
        }
    }
    public function roles()
    {
        return $this->belongsToMany(Roles::class, 'access');
    }
}
