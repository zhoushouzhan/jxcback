<?php
namespace mod;
trait Article {


//关联地区
public function setAreaAttr($value, $data) {
    $arr=json_decode($value,true);

	return implode(',',$arr);
}

			}
