<?php
namespace app\common\model;
use think\Model;
use think\facade\Db;
class Kucundan extends Model {
    use \mod\Kucundan;
    /**
     * 1:正常
     * 2：异常
     * 3：出售中
     * 4：己售
     */
    public static function onAfterWrite($data)
    {
        //入库
        if($data['type']==1&&$data['enabled']){
           self::importgoods($data);
        }
        //销售
        if($data['type']==2&&$data['enabled']){
            self::exportgoods($data);
        }
        //调拔
        if($data['type']==3&&$data['enabled']){
            self::movegoods($data);
        }

    }

    public static function onAfterDelete($data)
    {
        $id=$data['id'];
        //删除入库单时删除货品明细
        if($data['type']==1){
            db::name('goodsitem')->where('source_id',$id)->delete();
        }
    }


    public function setBillAttr($value,$data){
        if(is_array($value)){
            return json_encode($value);
        }
    }

    public function getBillAttr($value,$data){
        if($value){
            return json_decode($value,true);
        }
    }
    public function getGoodsCountAttr($value,$data){
        $count=0;
        if($data['bill']){
            $bill=json_decode($data['bill']);
            foreach($bill as $v){
                $count+= intval($v->numbers);
            }
        }
        return $count;
    }
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function member(){
        return $this->belongsTo(Member::class);
    }

    public function getTypeTipAttr($value,$data){
        $arr=['1'=>'入库','2'=>'出库'];
        return $arr[$data['type']]? $arr[$data['type']]:'';
    }
    //入库
    public static function importgoods($data){

        //$id=$data->id;
        //db::name('goodsitem')->where('source_id',$id)->delete();
        $insertData=[];
        $time=time();
        $goods_ids=[];
        foreach($data['bill'] as $v){
            $goods_ids[]=$v['goods_id'];
            $v['source_id']=$data['id'];
            $v['create_time']=$time;
            $v['status']=1;
            unset($v['thumbFile']);
            $updateGoodsStoreprice=['id'=>$v['goods_id'],'storeprice'=>$v['storeprice']];
            db::name('goods')->save($updateGoodsStoreprice);
            unset($v['storeprice']);
            $maxcount=$v['numbers'];
            unset($v['numbers']);
            for($i=0;$i<$maxcount;$i++){
                $insertData[]=$v;
            }
        }
        if($insertData){
            db::name('goodsitem')->insertAll($insertData);
        }
        //更新商品库存字段
        updateStock($goods_ids);
    }
    //销售
    public static function exportgoods($data){
        $id=$data->id;
        $member_id=$data['member_id'];
        $time=time();
        foreach($data['bill'] as $v){
            $maxcount=$v['numbers'];
            $goods_id=$v['goods_id'];
            $sellprice=$v['sellprice'];
            db::name('goodsitem')->where('goods_id',$goods_id)->exp('profit',"$sellprice-inprice")->limit($maxcount)->order('id','asc')->update([
                'sell_id'=>$id,
                'status'=>4,
                'sellprice'=>$sellprice,
                'out_time'=>$time,
                'member_id'=>$member_id
            ]);
        }
    }
    //调拔
    public static function movegoods($data){
        $godown_id=$data['godown_id'];
        $time=time();
        foreach($data['bill'] as $v){
            $maxcount=$v['numbers'];
            $goods_id=$v['goods_id'];
            db::name('goodsitem')->where('goods_id',$goods_id)->limit($maxcount)->order('id','asc')->update([
                'source_id'=>$data['id'],
                'godown_id'=>$godown_id,
                'update_time'=>$time
            ]);
        }
    }

    //获取订单金额
    public function getSumpriceAttr($value,$data){
        $total=0;
        $bill=$data['bill'];
       
        $bill=json_decode($bill,true);

        foreach($bill as $v){
            $total+=$v['numbers']*$v['sellprice'];
        }
        return number_format($total,2);;
    }
}
?>