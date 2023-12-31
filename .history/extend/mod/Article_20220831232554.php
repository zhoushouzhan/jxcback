<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-08-14 10:58:21
 * @LastEditTime: 2022-08-31 23:25:18
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

		halt($va)
		$arr = json_decode($value, true);

		return implode(',', $arr);
	}
}
