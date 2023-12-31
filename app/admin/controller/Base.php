<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-21 10:09:47
 * @LastEditTime: 2022-10-05 21:13:36
 * @FilePath: \web\app\admin\controller\base.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

namespace app\admin\controller;

use app\BaseController;
use think\facade\Cache;
use think\facade\Db;
use think\facade\Config;

class Base extends BaseController
{
    //protected $middleware = ['check' => ['except' => ['login']]];
    protected $admin;
    protected $site=[];
    // 初始化
    protected function initialize()
    {
        parent::initialize();
        $this->checkAuth();
        $this->getSystem();
    }
    //系统配置信息
    protected function getSystem()
    {

        if (!$this->site = Cache::get('sitepro')) {
            $this->site = Db::name('sitepro')->where('id', '1')->find();

            Cache::set('sitepro', $this->site);
        }
        $this->site['model'] = Config::get('app.sysModel');
        $this->site['admin'] = $this->admin;


    }
    protected function checkAuth()
    {
        if (isset($this->request->adminid)) {
            $this->admin = \app\common\model\Admin::with('roles')->where('status',1)->withoutField('password,salt')->find($this->request->adminid);

  

            if(!$this->admin){
                $this->error('账号己锁定,请联系管理员');
            }
            $this->admin->routes = $this->getMenu();
        }
    }
    //获取路由
    protected function getMenu($type = '')
    {
        //权限筛选
        $map = [];
        //$map[]=['status','=',1];
        //超管权限最高
        if($this->admin->id!=1){
            $rules = [];
            foreach ($this->admin->roles as $v) {
                if (is_array($v->rules)) {
                    $rules = array_merge($rules, $v->rules);
                }
            }
            $rules = array_unique($rules);
            $map[]=['id','in',$rules];
        }
        $dataList = \app\common\model\Rule::with('mod')->withoutField('name')->where($map)->order('sort', 'asc')->select()->toArray();
        $ruleList = layoutData($dataList);
        return $ruleList;
    }

}
