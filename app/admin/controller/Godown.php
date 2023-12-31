<?php
declare (strict_types = 1);

namespace app\admin\controller;


class Godown extends Base
{

    protected $mod;
    public function initialize()
    {
        parent::initialize();
        $this->mod = \app\common\model\Godown::class;
    }


    public function index($keyword='',$limit=30,$page=0)
    {
       
        $dataList =  $this->mod::order('id', 'desc')->with(['goods'])->append(['adminlist'])->paginate($limit, false, ['page' => $page, 'query' => ['keyword' => $keyword]]);
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
