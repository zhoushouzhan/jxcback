<?php
namespace app\common\model;
use think\Model;
class Godown extends Model {
    use \mod\Godown;
    //自定义内容
    public function setAdminAttr($value,$data){
        if(is_array($value)){
            $value=array_filter($value);

            return implode(',',$value);
        }
    }
    public function getAdminAttr($value,$data){
        $arr=explode(',',$value);
        $ids = array_map('intval', $arr);
        return $ids;
    }
    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }
    public function getAdminlistAttr($value,$data){
        return Admin::where('id','in',$data['admin'])->select();
    }
}
?>