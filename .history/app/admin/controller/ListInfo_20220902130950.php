<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-08-03 11:36:52
 * @LastEditTime: 2022-09-02 11:58:41
 * @FilePath: \web\app\admin\controller\ListInfo.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

declare(strict_types=1);

namespace app\admin\controller;

class ListInfo extends Base
{


    public function initialize()
    {
        parent::initialize();
        $modid = $this->request->param('modid');
        $this->categoryid = $this->request->param('categoryid');
        if (!$modid) {
            $this->error('模型ID错误');
        }
        $this->mod = \app\common\model\Mod::find($modid);

        $this->table = $this->mod->getMod();
    }

    public function getList(int $isremove = 0, $page = 1, $keyword = '', $limit = 10)
    {
        if ($this->mod['type'] == 'form') {
            $data = $this->table::find(1);
        }
        if ($this->mod['type'] == 'classic') {

            $map = [];

            if ($this->categoryid) {
                $map[] = ['title', 'like', "%{$keyword}%"];
            }

            if (!empty($keyword)) {
                $map[] = ['title', 'like', "%{$keyword}%"];
            }

            $removeNum = $this->table::onlyTrashed()->count();
            if ($isremove) {
                $res = $this->table::onlyTrashed()->where($map)->order('id', 'desc')->paginate($limit, false, ['page' => $page]);
            } else {
                $res = $this->table::order('id', 'desc')->where($map)->paginate($limit, false, ['page' => $page]);
            }
            $data['removeNum'] = $removeNum;
            $data['list'] = $res;
        }

        $this->success('获取成功', $data);
    }
    public function save()
    {
        $data = input('data');

        if (isset($data['id']) && $data['id']) {
            if ($r = $this->table::update($data)) {
                $this->success('更新成功', $r);
            }
        } else {
            if ($r = $this->table::create($data)) {
                $this->success('保存成功', $r);
            }
        }
    }
    /*对于初次删除的存回收站*/
    public function delete($ids = [])
    {

        if (empty($ids)) {
            $this->error('请选择项目');
        }
        $res = $this->table::withTrashed()->where('id', 'in', $ids)->select();
        foreach ($res as $k => $v) {
            if ($v->delete_time) {
                $v->force()->delete();
            } else {
                $v->delete();
            }
        }
        $this->success('删除完毕');
    }
    /**还原 */
    public function restore($ids)
    {
        $res = $this->table::withTrashed()->where('id', 'in', $ids)->select();
        foreach ($res as $k => $v) {
            $v->restore();
        }
        $this->success('己还原');
    }
    /**导出 */
    public function export($ids = [])
    {
        $dataList = $this->table::withTrashed()->where('id', 'in', $ids)->select();
        $header =  $this->mod['modcolumn'];
        app('Ypexcel')->toxlsx($header, $dataList);
    }
    /**导入 */
    public function import()
    {
        $file = $this->request->file('file');

        if (!$file) {
            $this->error('未上传文件');
        }
        $filePath = $file->getPathname();
        $cols = $this->mod['modcolumn'];
        $data = app('Ypexcel')->formxlsx($filePath, $cols);
        $objmod = new $this->table;
        if ($res =  $objmod->saveAll($data)) {
            $this->success('导入完毕');
        } else {
            $this->error('导入失败');
        }
    }
}
