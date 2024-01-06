<?php

declare(strict_types=1);

namespace app\admin\controller;
use think\facade\Db;
class Goodsitem extends Base
{
    protected $mod;
    public function initialize()
    {
        parent::initialize();
        $this->mod = \app\common\model\Goodsitem::class;
    }
    public function index($keyword='',$status=0,$limit = 10, $page = 1)
    {
        $map = [];
        if($keyword){
            $map[]=['title|code','like',"%$keyword%"];
        }
        if($status){
            $map[]=['status','=',$status];
        }
        //排序 
        $order=[];

        $orderby=input('orderby');
        $ordersort=input('ordersort');
        if($orderby){
            $order=[$orderby=>$ordersort];
        }else{
            $order=['id'=>'desc'];
        }
        
        
        $factory_id=input('factory_id');
        if($factory_id){
            $factory_goods=db::name('goods')->where('factory_id',$factory_id)->column('id');
            $map[]=['goods_id','in',$factory_goods];
        }


        //排序 
        $order=[];

        $orderby=input('orderby');
        $ordersort=input('ordersort');
        if($orderby){
            $order=[$orderby=>$ordersort];
        }else{
            $order=['id'=>'desc'];
        }
        $dataList = $this->mod::where($map)->order($order)->with(['category','godown','goods'])->paginate($limit, false, ['page' => $page, 'query' => []]);
        $this->success('获取成功', $dataList);
    }

    public function details($code=''){
        if($code==''){
            $this->error('请输入条码');
        }
        $map=[];
        $map[]=['code','=',$code];
        $map[]=['status','=',1];

        $r=$this->mod::with('category')->where($map)->find();
        if($r){
            $this->success('获取成功',$r);
        }else{
            $this->error('查无此货');
        }
    }


    public function save($id=0){
        $data=input('post.');
        $res=$this->mod::update($data);
        $this->success('更新成功');

    }

    public function read($id=0){

        $r=$this->mod::with(['goods'])->find($id);
        if($r){
            $this->success('获取成功',$r);
        }else{
            $this->error('查无此货');
        }
    }

    public function getcount(){
        $count= $this->mod::where('status',1)->count();
        $this->success('获取成功',$count);
    }

    public function delete($id)
    {

        if (!$id) {
            $this->error('参数错误');
        } else {
            if ($this->mod::destroy($id)) {
                $this->success('删除成功');
            } else {
                $this->error('删除失败');
            }
        }
    }

    public function near($goods_id=0){
        $map=[['goods_id','=',$goods_id]];
        $dataList=$this->mod::where($map)->with(['goods'])->limit(10)->group('source_id')->order('id','desc')->select();
        $this->success('获取成功',$dataList);
    }



    public function label($goods_id=0){
        $dataList=[];
        $ids=[];
        $r=\app\common\model\Goods::find($goods_id);
        if($r->label){
            $ids= \app\common\model\Goods::where('label',$r->label)->where('id','<>',$goods_id)->column('id');
        }



        $map=[['goods_id','in',$ids]];
        $dataList=$this->mod::where($map)->with(['goods'])->limit(10)->group('source_id')->order('id','desc')->select();
        $this->success('获取成功',$dataList);



    }

}