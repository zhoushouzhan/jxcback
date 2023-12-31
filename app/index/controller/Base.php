<?php

namespace app\index\controller;

use app\BaseController;
use think\facade\Cache;
use think\facade\Db;
use think\facade\View;
use think\facade\Session;
use think\facade\Cookie;

class Base extends BaseController
{
    protected $site = [];
    protected $member = [];
    protected $category = [];
    // 初始化
    protected function initialize()
    {
        parent::initialize();
        $this->getSystem();
        $this->getNav();
        $this->getMember();
    }
    //会员信息
    protected function getMember()
    {
        $this->uid = Session::get('uid') ? Session::get('uid') : Cookie::get('uid');
        if ($this->uid) {
            $this->member = \app\common\model\Member::find($this->uid);
        }

        View::assign("member",  $this->member);
    }
    //系统配置信息
    protected function getSystem()
    {
        if (!$this->site = Cache::get('sitepro')) {
            $this->site = Db::name('sitepro')->where('id', '1')->find();
            Cache::set('sitepro', $this->site);
        }
        View::assign([
            'site'  => $this->site,
        ]);
    }
    //获取导航
    protected function getNav()
    {
        if (Cache::has('category')) {
            $this->category = Cache::get('category');
        } else {
            $this->category = \app\common\model\Category::with('mod')->select();
        }
        View::assign([
            'category' => layoutCategory($this->category),
            'nav'  => nav($this->category)
        ]);
    }
}
