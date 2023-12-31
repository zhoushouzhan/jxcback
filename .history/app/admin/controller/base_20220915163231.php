<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-21 10:09:47
 * @LastEditTime: 2022-09-15 16:32:31
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
    //获取路由
    protected function getMenu()
    {
        $dataList = \app\common\model\Rule::with('mod')->order('sort', 'asc')->select()->toArray();
        //取内容节点的ID
        $content_node_id = 0;
        foreach ($dataList  as $k => $v) {
            if ($v['name'] == 'Content') {
                $content_node_id = $v['id'];
            }
        }
        //栏目嵌入菜单
        $category = \app\common\model\Category::with('mod')->select()->toArray();
        $newMenu = [];
        foreach ($category as $k => $v) {
            $newitem['id'] =  $content_node_id . $v['id']; //拼接
            $newitem['title'] = $v['title'];
            $newitem['icon'] = '';
            $newitem['mod'] = '';
            $newitem['mod_id'] = $v['mod_id'];
            $newitem['path'] = '/Listinfo/' . $v['mod']['name'] . '/' . $v['id'];
            $newitem['component'] = '/Listinfo/index.vue';
            $newitem['pid'] =  ($v['pid'] == 0 ? $content_node_id : $content_node_id . $v['pid']); //拼接父ID
            $newMenu[] = $newitem;
        }
        $categoryList = layoutData($newMenu);
        $ruleList = layoutData($dataList);
        foreach ($ruleList  as $k => $v) {
            if ($v['name'] == 'Content') {
                $v['children'] = $categoryList;
            }
            $ruleList[$k] = $v;
        }
        return $ruleList;
    }
}
