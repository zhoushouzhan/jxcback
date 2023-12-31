<?php
namespace mod;
trait Sitepro {


//关联地区
public function getAreaAttr($value, $data) {
	return \app\common\model\Classify::getSelected('168',$value,$data);
}

//关联结束时间
public function setEndtimeAttr($value, $data) {
	return strtotime($value);
}
public function getEndtimeAttr($value, $data) {
	if($value==0?)
	return date('Y-m-d H:i:s',$value);
}
			}
?>