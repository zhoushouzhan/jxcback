<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-21 09:57:50
 * @LastEditTime: 2022-09-15 09:14:39
 * @FilePath: \web\app\admin\controller\Rule.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

namespace app\admin\controller;

use app\admin\validate\CheckRule;
use think\exception\ValidateException;

class Rule extends Base
{
    public function index()
    {

        $dataList = \app\common\model\Rule::with('mod')->order('sort', 'asc')->select()->toArray();

        $contentnode
        foreach ($dataList  as $k => $v) {
            if ($v['name'] == 'Content') {
                $v['children'] = $categoryList;
            }
            $ruleList[$k] = $v;
        }

        //栏目嵌入菜单
        $category = \app\common\model\Category::with('mod')->select()->toArray();

        $newMenu = [];
        foreach ($category as $k => $v) {
            $newitem['id'] =  'class' . $v['id'];
            $newitem['title'] = $v['title'];
            $newitem['icon'] = '';
            $newitem['mod'] = '';
            $newitem['mod_id'] = $v['mod_id'];
            $newitem['path'] = '/Listinfo/' . $v['mod']['name'] . '/' . $v['id'];
            $newitem['component'] = '/Listinfo/index.vue';
            $newitem['pid'] =  ($v['pid'] == 0 ? 0 : 'class' . $v['pid']);
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

        $this->success('', $ruleList);
    }
    public function save()
    {
        $data = $this->request->param();
        try {
            //验证
            $valCheck = validate(CheckRule::class)->scene('add')->check($data);
            if ($valCheck !== true) {
                $this->error($valCheck);
            }
        } catch (ValidateException $e) {
            $this->error($e->getError());
        }
        if (\app\common\model\Rule::create($data)) {
            $this->success('添加成功');
        } else {
            $this->error('添加失败');
        }
    }
    public function update()
    {
        $data = $this->request->param();
        try {
            //验证
            $valCheck = validate(CheckRule::class)->scene('edit')->check($data);
            if ($valCheck !== true) {
                $this->error($valCheck);
            }
        } catch (ValidateException $e) {
            $this->error($e->getError());
        }
        if (\app\common\model\Rule::update($data)) {
            $this->success('更新成功');
        } else {
            $this->error('更新失败');
        }
    }
    public function delete($id)
    {
        if (\app\common\model\Rule::destroy($id)) {
            $this->success('删除成功', $this->index());
        } else {
            $this->error('删除失败');
        }
    }
}
