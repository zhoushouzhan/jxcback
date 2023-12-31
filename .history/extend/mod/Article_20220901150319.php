<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-08-14 10:58:21
 * @LastEditTime: 2022-09-01 15:02:56
 * @FilePath: \web\extend\mod\Article.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

namespace mod;

trait Article
{


	//关联地区
	public function setAreaAttr($value, $data)
	{
		halt($value)
		//return implode(',',$value);
	}


	//关联结束时间
	public function setEndtimeAttr($value, $data)
	{
		return strtotime($value);
	}
	public function getEndtimeAttr($value, $data)
	{
		$value = $value ? $value : time();
		return date('Y-m-d H:i:s', $value);
	}
}
