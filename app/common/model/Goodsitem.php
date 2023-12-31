<?php
namespace app\common\model;
use think\Model;
use think\facade\Db;
class Goodsitem extends Model {
use \mod\Goodsitem;
//自定义内容

    public static function  onBeforeUpdate($data)
    {
        if($data['storeprice']){
            $updateGoodsStoreprice=['id'=>$data['goods_id'],'storeprice'=>$data['storeprice']];
            db::name('goods')->save($updateGoodsStoreprice);
        }
        unset($data['goods_id']);
        unset($data['storeprice']);
        return $data;
    }
    //删除库存商品事件
    public static function onAfterDelete($data){
        //删除商品更新库存字段
        updateStock([$data['goods_id']]);
    }

    //关联栏目信息
    public function category(){
        return $this->belongsTo(Category::class);
    }
    //关联仓库
    public function godown(){
        return $this->belongsTo(Godown::class);
    }
    public function getInpriceAttr($value,$data){
        return number_format($value,2);
    }
    public function getSellpriceAttr($value,$data){
        return number_format($value,2);
    }
    public function goods(){
        return $this->belongsTo(Goods::class)->with(['factory'])->append(['thumbFile']);
    }
    
}
?>