<?php

declare(strict_types=1);

namespace app\admin\controller;

use think\exception\ValidateException;

class Article extends Base
{
    protected $mod;
    public function initialize()
    {
        parent::initialize();
        $this->mod = \app\common\model\Article::class;
    }
    public function index(int $isremove = 0,$keyword = '', $limit = 20, $page = 1)
    {
        $map = [];
        if ($keyword) {
            $map[] = ['title', 'like', "%$keyword%"];
        }

        if ($isremove) {
            $dataList = $this->mod::onlyTrashed()->where($map)->order('id', 'desc')->paginate($limit, false, ['page' => $page, 'query' => ['keyword' => $keyword]]);
        } else {
            $dataList = $this->mod::where($map)->order('id', 'desc')->paginate($limit, false, ['page' => $page, 'query' => ['keyword' => $keyword]]);
            $data['removeNum'] = $this->mod::onlyTrashed()->where($map)->count();
        }
        $this->success('获取成功', $dataList);
    }
    public function details($id)
    {
        $r = $this->mod::find($id);
        $this->success('获取成功', $r);
    }
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
    /*对于初次删除的存回收站*/
    public function delete($ids = [])
    {
        if (empty($ids)) {
            $this->error('请选择项目');
        }
        $res = $this->mod::withTrashed()->where('id', 'in', $ids)->select();
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
        $res = $this->mod::withTrashed()->where('id', 'in', $ids)->select();
        foreach ($res as $k => $v) {
            $v->restore();
        }
        $this->success('己还原');
    }

    /**导出 */
    public function export($ids = [])
    {
        $dataList = $this->mod::where('id', 'in', $ids)->select();
        $mod=\app\common\model\Mod::where('name','article')->find();
        $header =  $mod->modcolumn;
        app('Ypexcel')->toxlsx($header, $dataList);
    }

}
