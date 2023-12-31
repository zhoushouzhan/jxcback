<?php
namespace mod;
trait Sitepro {


//关联结束时间
public function setEndtimeAttr($value, $data) {
	return strtotime($value);
}
public function getEndtimeAttr($value, $data) {
	return date('Y-m-d H:i:s',$value);
}

//关联地区
public function getAreaAttr($value, $data) {
	return \app\common\model\Classify::getSelected('',$value,$data);
}
			}
?>