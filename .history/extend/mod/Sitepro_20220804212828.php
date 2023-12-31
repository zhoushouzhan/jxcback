<?php
namespace mod;
trait Sitepro {


//关联结束时间单图片
public function getEndtimeAttr($value, $data) {
	return \app\common\model\Files::find($value);
}

//关联地区下拉
	public function getAreaAttr($value, $data) {
		return \app\common\model\Mclass::where('id', $value)->find();
	}
			}
