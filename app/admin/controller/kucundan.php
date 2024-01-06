<?php
declare (strict_types = 1);

namespace app\admin\controller;


class Kucundan extends Base
{

    protected $mod;
    public function initialize()
    {
        parent::initialize();
        $this->mod = \app\common\model\Kucundan::class;
    }

    public function index($keyword='',$limit=20,$page=0)
    {
        $dataList =  $this->mod::order('id', 'desc')->with(['admin'])->append(['goodsCount','typeTip'])->paginate($limit, false, ['page' => $page, 'query' => ['keyword' => $keyword]]);
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
                $data['admin_id']=$this->admin->id;
                $data['sn']=date('YmdHis').rand(111,999);
                $this->mod::create($data);
                $this->success('录入成功');
            }
        }
    }

    public function read($id)
    {
        $r = $this->mod::with('admin')->append(['goodsCount','typeTip'])->find($id);
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
