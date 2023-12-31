<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-21 09:57:50
 * @LastEditTime: 2022-09-26 08:51:42
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
        //这里不需要栏目
        $ruleList = $this->getMenu('noCategory');
        $this->success('', $ruleList);
    }
    public function getrule($id)
    {
        $rule = \app\common\model\Rule::find($id);
        $this->success('', $rule);
    }

    public function save()
    {
        $data = $this->request->param();
        $tips = '增加成功';
        $scene = 'add';
        if (isset($data['id'])) {
            $tips = '更新成功';
            $scene = 'edit';
        }
        try {
            //验证
            $valCheck = validate(CheckRule::class)->scene($scene)->check($data);
            if ($valCheck !== true) {
                $this->error($valCheck);
            }
        } catch (ValidateException $e) {
            $this->error($e->getError());
        }
        $rule = new \app\common\model\Rule;
        if ($rule->replace()->save($data)) {
            $this->success($tips);
        } else {
            $this->error('操作失败');
        }
    }
    public function delete($id)
    {
        if (\app\common\model\Rule::destroy($id)) {
            $this->success('删除成功', $this->getMenu());
        } else {
            $this->error('删除失败');
        }
    }
}
