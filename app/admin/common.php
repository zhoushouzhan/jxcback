<?php
use think\facade\Db;
function updateStock($ids){
        //更新商品库存字段
        foreach($ids as $v){
            db::name('goods')->update([
                'id'=>$v,
                'stock'=>db::name('goodsitem')->where('goods_id',$v)->count()
            ]);
        }

}
