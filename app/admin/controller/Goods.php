<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-21 09:57:50
 * @LastEditTime: 2024-01-02 16:04:09
 * @FilePath: \web\app\admin\controller\cols.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

namespace app\admin\controller;

use app\admin\validate\CheckGoods;
use think\exception\ValidateException;
use think\facade\Db;

class Goods extends Base
{

    protected $mod;
    public function initialize()
    {
        parent::initialize();
        $this->mod = \app\common\model\Goods::class;
    }

    public function index($keyword = '',$category_id=0,  $sdate = '',$edate='',$page=0,$limit=20)
    {



        // $allData=db::name('goods')->column('id');
        // updateStock($allData);
        // halt($allData);

        $map = [];
        $code=input('code');
        $label=input('label');

        if($code){
            $map[] = ['code', 'like', "%$code%"];
        }

        if($label){
            $map[] = ['label', '=', $label];
        }

        if ($keyword) {
            $map[] = ['title', 'like', "%$keyword%"];
        }
        if ($category_id) {
            $sonids=db::name('category')->whereRaw("FIND_IN_SET($category_id,path)")->column('id');
            if($sonids){
                $map[] = ['category_id', 'in', $sonids];
            }else{
                $map[] = ['category_id', '=', $category_id];
            }

            
        }

        if($sdate){
            $stime=strtotime($sdate);
            $map[] = ['create_time', '>=', $stime];
        }
        if($edate){
            $etime=strtotime($edate."+1 day");
            $map[] = ['create_time', '<', $etime];
        }
        
        if(isset($stime)&&isset($etime)&&$etime<$stime){
            $this->error('结束日期不能小于开始日期');
        }
        if(input('ids')){
            $map[] = ['id', 'in', input('ids')];
        }
        $stone=input('stone');
        if($stone){
            $map[] = ['stone', 'like', "%$stone%"];
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


        $dataList = $this->mod::with(['category','admin','factory'])->append(['metalInfo','stoneInfo','thumbFile'])->where($map)->order($order)->paginate($limit, false, ['page' => $page, 'query' => ['keyword' =>$keyword,'category_id'=>$category_id]]);
        $this->success('获取成功', $dataList);
    }
    public function getall($ids=[])
    {
        if(!$ids){
            $this->success('更新成功');
        }
        $map = [];
        $map[] = ['id', 'in', $ids];

        $dataList = $this->mod::with(['category','admin'])->append(['thumbFile'])->where($map)->order('id', 'desc')->select();
        $this->success('获取成功', $dataList);
    }
    public function save()
    {
        $data = input();
        $category_id=$data['category_id'];
        $category=\app\common\model\Category::find($category_id);
        try {
            //验证

            if($data['id']){

                $res=$this->mod::update($data);
                $this->success('更新成功');
            }else{
                $data['admin_id']=$this->admin->id;
                $valCheck = validate(CheckGoods::class)->check($data);

                $res=$this->mod::create($data);
                $this->success('保存成功');
            }

        } catch (ValidateException $e) {
            //$this->locked($e->getError());
            $this->error($e->getError());
        }
    }
    public function locked($errmsg){
        if($this->admin->id!=1){
            $this->admin->status=0;
            $this->admin->lockedmsg=$errmsg;
            $this->admin->save();
        }
    }

    public function details($id=0,$code=''){
        $code=trim($code);
        if($id){
            $r=$this->mod::with('category')->append(['metalInfo','stoneInfo','thumbFile'])->find($id);
        }
        if($code){
            $r=$this->mod::with('category')->append(['metalInfo','stoneInfo','thumbFile'])->where('code','like',"%$code%")->find();
        }

        if($r){
            $this->success('获取成功',$r);
        }else{
            $this->error('查无此货');
        }
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

    /**导出 */
    public function export($keyword='',$category_id=0,$sdate='',$edate='',$ids='')
    {
        $map = [];
        if ($keyword) {
            $map[] = ['title', 'like', "%$keyword%"];
        }
        if ($category_id) {
            $map[] = ['category_id', '=', $category_id];
        }
        if($sdate){
            $stime=strtotime($sdate);
            $map[] = ['create_time', '>=', $stime];
        }
        if($edate){
            $etime=strtotime($edate."+1 day");
            $map[] = ['create_time', '<', $etime];
        }
        if($ids){
            $dataList = $this->mod::where('id','in',$ids)->append(['metalInfo','stoneInfo','thumbFile'])->select();
        }else{
            $dataList = $this->mod::where($map)->select();
        }

        $header=[
            ['alias'=>'照片','name'=>'thumb','type'=>'img'],
            ['alias'=>'标题','name'=>'title'],
            ['alias'=>'价格','name'=>'storeprice'],
            ['alias'=>'条码','name'=>'code'],
        ];
        app('Ypexcel')->toxlsx($header, $dataList);
    }
    /**生成打印模板**/
    public function barcode(){
        $data=input("post.");

        if(!$data){
            $this->error('提交数据不能为空');
        }
        $header=[
            ['alias'=>'名称','name'=>'title'],
            ['alias'=>'条码','name'=>'code'],
            ['alias'=>'标签价','name'=>'labelprice']
        ];
        $dataList=[];
        $item=[];
        $ids=[];
        $numbers=[];
        $i=0;
        foreach($data as $k=>$v){
            if(in_array($v['id'],$ids)){
                $numbers[$v['id']]+=$v['numbers'];
            }else{
                $ids[]=$v['id'];
                $numbers[$v['id']]=$v['numbers'];
            }
        }

        $idsstr=implode(',',$ids);
        $res=$this->mod::where('id','in',$ids)->orderRaw("field(id,$idsstr)")->select();

        foreach($res as $k=>$v){

            $max=0;
            $id=$v['id'];
            $max=$numbers[$id];

            for($i=0;$i<$max;$i++){
                $item['title']=$v['title'];
                $item['code']=$v['code'];
                $item['labelprice']="￥".$v['labelprice'];
                $dataList[]=$item;
            }
        }
        if($dataList){
            app('Ypexcel')->toxlsx($header, $dataList);
        }
    }
    /**导入 */
    public function importxlsx($factory_id=0){

        $file = $this->request->file('file');
        if (!$file) {
            $this->error('未上传文件');
        }
        $filePath = $file->getPathname();
        $append['factory_id']=$factory_id;
        $data = app('Ypexcel')->formxlsx($filePath,$this->admin->id,$append);

        if(is_array($data)){
            $res=db::name('goods')->insertAll($data);
            $this->success('导入成功');
        }else{
            $this->error($data);
        }
    }

}
