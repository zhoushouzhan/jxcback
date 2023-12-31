<?php
// +----------------------------------------------------------------------
// | 一品内容管理系统 [ YPCMS ]
// +----------------------------------------------------------------------
// | 版权所有 2016~2019 东海县一品网络技术有限公司
// +----------------------------------------------------------------------
// | 官方网站: http://www.yipinjishu.com
// +----------------------------------------------------------------------
declare(strict_types=1);

namespace app\common\model;

use app\common\model\Roles;
use think\Model;

/**
 * 后台管理员模型
 * @package app\admin\model
 */
class Admin extends Model
{
    //开启自动时间缀
    protected $autoWriteTimestamp = true;

    protected $type = [
        'jobtime' => 'timestamp:Y-m-d',
        'birthday' => 'timestamp:Y-m-d',
        'biyetime' => 'timestamp:Y-m-d',
        'worktime' => 'timestamp:Y-m-d',
    ];

    //数据更新前事件
    public static function onBeforeUpdate($data)
    {
        if (isset($data['id'])) {
            //旧数据
            $oldData = self::find($data['id']);
            //更新部分权限
            if (isset($data['roles_id'])) {
                //获取管理员权限
                $r = self::find($admin_id);
                $haveRoles = []; //管理员权限

                foreach ($r->roles as $key => $value) {
                    $haveRoles = array_merge($value->roles_id, $haveRoles);
                }
                //原有权限
                $oldRoles = array_column($oldData->roles->toArray(), 'id');
                //提交的权限
                $post_roles = $data['roles_id'];
                //在原有权限中去掉管理员权限中未提交的部分
                //1去除所有管理员权限
                foreach ($oldRoles as $key => $value) {
                    if (in_array($value, $haveRoles)) {
                        unset($oldRoles[$key]);
                    }
                }

                //2增加提交的权限
                $new_roles = array_merge($oldRoles, $post_roles);
                //3防止有重复
                $new_roles = array_unique(array_map('intval', $new_roles));

                $data['roles_id'] = $new_roles;
            }
        }
    }

    //数据写入后事件
    public static function onAfterWrite($data)
    {
        //角色
        if (isset($data['roles_id'])) {
            $data->roles()->detach();
            if ($data['roles_id']) {
                $data->roles()->saveAll($data['roles_id']);
            }
        }
    }

    //数据删除后事件
    public static function onAfterDelete($data)
    {
        //删除角色
        $data->roles()->detach();
    }

    public static function login($data)
    {
        $where[] = ['username', '=', $data['username']];
        $where[] = ['status', '=', 1];
        if ($admin = self::field()->where($where)->find()) {
            if ($admin->password != md5($data['password'] . $admin->salt)) {
                return 0;
            } else {
                $admin->last_ip = $data['ip'];
                $admin->save();
                return $admin;
            }
        } else {
            return -1;
        }
    }


    public function roles()
    {
        return $this->belongsToMany(Roles::class, 'access');
    }


    public static function appendForm($data)
    {
        return '';
    }
}
