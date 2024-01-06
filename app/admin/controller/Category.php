<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-08-23 14:10:32
 * @LastEditTime: 2022-09-02 10:28:24
 * @FilePath: \web\app\admin\controller\Category.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

declare(strict_types=1);

namespace app\admin\controller;

use think\facade\Session;
use think\exception\ValidateException;
use think\facade\Db;
class Category extends Base
{
    protected $mod;
    protected function initialize()
    {
        parent::initialize();
        $this->mod = new \app\common\model\Category;
    }
    //获取列表
    public function getList()
    {
        $res = $this->mod::order('sort', 'asc')->order('id', 'asc')->select();
        $this->success('获取成功', layoutCategory($res));
    }
    //获取单个栏目
    public function getDetails($id)
    {
        session::flash("category","getpathinfo");
        if ($res = $this->mod::with('mod')->find($id)) {
            $this->success('获取成功', $res);
        } else {
            $this->error('获取栏目' + $id + '失败');
        }
    }
    //下拉菜单
    public function getSelect($pid = 0, $ids = '')
    {
        if ($this->mod->getSelected($pid, $ids)) {
            $this->success('获取栏目成功', $this->mod->getSelected($pid, $ids));
        } else {
            $this->success('无子栏目');
        }
    }
    //增加/更新栏目
    public function save()
    {
        $data = input();
        if (isset($data['id']) && $data['id']) {
            if ($r = $this->mod::update($data)) {
                $this->success('更新成功', $r);
            }
        } else {
            if ($r = $this->mod::create($data)) {
                $this->success('保存成功', $r);
            }
        }
    }
    public function readTree($pid=0){
        $map=[];
        if($pid){
            $map[]=['path','like',"%,{$pid},%"];
        }
        $items=$this->mod::where($map)->select()->toArray();
        $tree=generateTree($items,$pid);
        $this->success('ok', $tree);
    }
    public function delete($ids)
    {
        if (!$ids) {
            return 0;
        }
        if (is_array($ids)) {
            $ids = array_map('intval', $ids);
        }
        $rs = $this->mod::destroy($ids);
        if ($rs) {
            $this->success("删除成功");
        }
    }
}
