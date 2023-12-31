<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-08-23 13:59:28
 * @LastEditTime: 2022-09-01 15:04:03
 * @FilePath: \web\extend\mod\Category.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

namespace mod;

trait Category
{
	//关联地区
	public function setAreaAttr($value, $data)
	{
		return implode(',', $value);
	}
}
