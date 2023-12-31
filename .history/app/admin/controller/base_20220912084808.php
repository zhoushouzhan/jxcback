<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-21 10:09:47
 * @LastEditTime: 2022-09-12 08:47:51
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
    protected $admin = [];

    // 初始化
    protected function initialize()
    {
        parent::initialize();
        $this->getSystem();
        $this->checkAuth();
    }
    //系统配置信息
    protected function getSystem()
    {

        if (!$this->site = Cache::get('sitepro')) {
            $this->site = Db::name('sitepro')->where('id', '1')->find();
            $this->site['sysMod'] = Config::get('app.sysMod');
            $this->site['modType'] = Config::get('app.modType');
            $this->site['actions'] = Config::get('app.actions');
            $this->site['formitem'] = Config::get('app.formitem');
            Cache::set('sitepro', $this->site);
        }
    }
    protected function checkAuth()
    {

        if (isset($this->request->adminid)) {
            $this->admin = \app\common\model\Admin::find($this->request->adminid);
        }
    }
    protected function getMenu()
    {
        $menu=\app\common\model\Rule
    }
}
