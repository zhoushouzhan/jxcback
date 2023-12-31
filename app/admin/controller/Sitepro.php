<?php
namespace app\admin\controller;
use think\exception\ValidateException;
class Sitepro extends base{
    protected $mod;
    public function initialize()
    {
        parent::initialize();
        $this->mod = \app\common\model\Sitepro::class;
    }
    public function getInfo($id=1)
    {
        try {
            $data=$this->mod::find($id);
            $this->success('查询成功',$data);
        } catch (ValidateException $e) {
            $this->error($e->getError());
        }
    }
    public function save(){
        try {
            $data=input('post.');
            $this->mod::update($data);
            $this->success('更新成功');
        } catch (ValidateException $e) {
            $this->error($e->getError());
        }
    }

}
