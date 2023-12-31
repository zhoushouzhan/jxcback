<?php
namespace mod;
trait Article {


//关联地区
public function setAreaAttr($value, $data) {
	return implode(',',$value);
}


//关联结束时间
public function setEndtimeAttr($value, $data) {
	return strtotime($value);
}
public function getEndtimeAttr($value, $data) {
    $value=$value?$value:time();
	return date('Y-m-d H:i:s',$value);
}
			}
