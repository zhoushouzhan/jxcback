<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-07-14 07:38:08
 * @LastEditTime: 2022-09-20 10:29:55
 * @FilePath: \web\app\admin\controller\Classify.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

declare(strict_types=1);

namespace app\admin\controller;
use think\facade\Session;
use think\exception\ValidateException;
use think\facade\Db;
class Classify extends Base
{

    protected $table;
    protected $data;

    protected function initialize()
    {
        parent::initialize();
        $this->table = new \app\common\model\Classify;
        $this->data = $this->table::select();
    }
    public function index($pid = 0)
    {

        $dataList = $this->table->where('pid', $pid)->select();
        $this->success('', $dataList);
    }
    public function getSelect($pid = 0, $ids = '')
    {
        Session::flash('classify','');
        if ($pid) {
            if ($res=$this->table->getSelected($pid, $ids)) {
                $this->success('获取分类成功', $res);
            } else {
                $this->success('无子分类');
            }
        } else {
            $this->error('未指定分类');
        }
    }
    public function getpathinfo($ids){
        $pathinfo='/';
        $r=$this->table::where('id','in',$ids)->column('title');
        if($r){
            $pathinfo=implode(',',$r);
        }
        return $pathinfo;
    }
    public function getTree($id)
    {
        $dataList = $this->table::order('sort', 'asc')->whereRaw('id !='.$id.' and not find_in_set('.$id.',path)')->select()->toArray();
        
        $ruleList = layoutCategory($dataList);
        $this->success('获取成功', $ruleList);
    }


    //获取所有子级
    public function getchild($id){
        $r=db::name('classify')->where('category_id',$id)->find();
      
        if(!$r){
            $this->success('获取成功',[]);exit;
        }
        $cid=$r['id'];
       
        $dataList=$this->table::whereRaw('find_in_set('.$cid.',path)')->select()->toArray();

        $ruleList = layoutCategory($dataList,$cid);
        $this->success('获取成功',$ruleList);
    }

    public function save()
    {
        if (input("?post.title")) {
            $titles = input('post.title');
            $pid = input('post.pid/d');
            $data=[];
            foreach (explode("\n", $titles) as $key => $value) {
                $arr = explode('|', $value);
                if (empty(trim($arr[0]))) {
                    continue;
                }

                if($this->table::where('title',trim($arr[0]))->find()){
                    continue;
                }
                $data[$key]['title'] = trim($arr[0]);
                $data[$key]['sort'] = isset($arr[1]) ? (int) $arr[1] : 0;
                $data[$key]['pid'] =  $pid;
            }

            if(empty($data)){
                $this->error('增加失败');
            }
            if ($this->table->saveAll($data)) {
                $this->success('增加成功');
            } else {
                $this->error('增加失败');
            }
        } else {
            $this->error('请输入分类名称');
        }
    }
    public function update()
    {
        $data = $this->request->param();
        if (empty($data['title'])) {
            $this->error('请输入标题');
        }
        if ($this->table::update($data)) {
            $this->success('更新成功');
        } else {
            $this->error('更新失败');
        }
    }
    public function delete($ids)
    {
        $data = $this->table->select();
        if (is_array($ids)) {
            $ids = array_map('intval', $ids); //数值转为整型
            if (count($ids) == 1) {
                $ids = (int) $ids[0];
            }
        }
        if ($this->table->destroy($ids)) {
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
}
