<?php
namespace app\common\model;
use think\Model;

class Goods extends Model {
    use \mod\Goods;
        //更新前
        public static function onBeforeUpdate($data)
        {
            unset($data['create_time']);
            unset($data['update_time']);
            unset($data['category']);
            //石头不选时搞空
            if(!$data['stone']){
                $data['stone']='';
            }
            //材质不选时搞空
            if(!$data['metal']){
                $data['metal']='';
            }

        }

        //模型事件-删除后
        public static function onAfterDelete($data)
        {  
            if($data['thumb']){
                Files::destroy($data['thumb']);
            }
        }
        //模型事件-删除后
        // public static function onAfterRead($data)
        // {  
        //     if($data['thumb']){
        //         Files::update([
        //             'id'=>$data['thumb'],
        //             'category_id'=>$data['category_id'],
        //             'tag'=>'thumb',
        //             'ypcms_id'=>$data['id'],
        //             'ypcms_type'=>'goods'
        //         ]);
        //     }
        // }
        //模型事件-写入后更新附件信息
        public static function onAfterWrite($data)
        {  
            if($data['thumb']){
                Files::update([
                    'id'=>$data['thumb'],
                    'category_id'=>$data['category_id'],
                    'tag'=>'thumb',
                    'ypcms_id'=>$data['id'],
                    'ypcms_type'=>'goods'
                ]);
            }
        }



        //关联栏目信息
        public function category(){
            return $this->hasOne(Category::class,'id','category_id');
        }

        public function admin(){
            return $this->hasOne(Admin::class,'id','admin_id');
        }
        public function godown(){
            return $this->hasOne(Godown::class);
        }

        public function setMetalAttr($value,$data){
            if(is_array($value)){
                return implode(',',$value);
            }else{
                return '';
            }
        }

        public function setStoneAttr($value,$data){
            if($value){
                return implode(',',$value);
            }else{
                return '';
            }
        }

        public function getMetalAttr($value,$data){
            if($value){
                $arr=explode(',',$value);
                $arr=array_map('intval',$arr);
                return $arr;
            }else{
                return [];
            }
        }

        public function getStoneAttr($value,$data){
            if($value){
                $arr=explode(',',$value);
                $arr=array_map('intval',$arr);
                return $arr;
            }else{
                return [];
            }
        }

        public function getStorepriceAttr($value,$data){
            return number_format($value,2);
        }

        //金属材质
        public function getMetalInfoAttr($value,$data){
            return Classify::where('id','in',$data['metal'])->select();
        }
        //原石材质
        public function getStoneInfoAttr($value,$data){
            return Classify::where('id','in',$data['stone'])->select();
        }
        public function getSclassinfoAttr($value,$data){
            $sclassinfo=[];
            if($data['sclass']){
                $sclassinfo=Classify::where('id','in',$data['sclass'])->select();
            }

            return $sclassinfo;
        }

        public function getThumbFileAttr($value,$data){
            if($data['thumb']&&$file=Files::find($data['thumb'])){
                return $file->filepath;
            }
        }

        //标签价格生成
        public function getLabelpriceAttr($value,$data){
            $rule=[
                1=>1,
                2=>1,
                3=>2,
                4=>3,
                5=>4,
                6=>4,
                7=>4,
                8=>5,
                9=>5
            ];
            $storeprice=$data['storeprice'];
            if($storeprice){
                $key=substr($storeprice,0,1);
                $labelprice=$rule[$key].$storeprice;
                return number_format($labelprice,2);
            }else{
                return 0;
            }

        }
        //厂商
        public function factory(){
            return $this->belongsTo(Factory::class);
        }
}
?>