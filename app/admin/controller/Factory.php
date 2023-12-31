<?php
declare (strict_types = 1);

namespace app\admin\controller;


class Factory extends Base
{

    protected $mod;
    public function initialize()
    {
        parent::initialize();
        $this->mod = \app\common\model\Factory::class;
    }

    public function index($keyword='',$limit=12,$page=0)
    {

        $map=[];
        if($keyword){
            $map[]=['title','like',"%$keyword%"];
        }

        $dataList =  $this->mod::order('id', 'desc')->where($map)->paginate($limit, false, ['page' => $page, 'query' => ['keyword' => $keyword]]);
        $this->success('获取成功', $dataList);
    }

    public function save($id=0)
    {
        if($this->request->isPost()){
            $data=input();
            if($id){
                $this->mod::update($data);
                $this->success('更新成功');
            }else{
                $this->mod::create($data);
                $this->success('添加成功');
            }
        }
    }

    public function read($id)
    {
        $r = $this->mod::find($id);
        $this->success('获取成功', $r);
    }

    public function delete($ids)
    {
        if (empty($ids)) {
            $this->error('请选择项目');
        }
        $this->mod::destroy($ids);
        $this->success('删除完毕');
    }
}
