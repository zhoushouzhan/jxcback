<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-21 09:57:50
 * @LastEditTime: 2022-07-24 15:39:15
 * @FilePath: \web\app\provider.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

use app\ExceptionHandle;
use app\Request;
use yp\Model;
use yp\File;
use yp\Allexport;
// 容器Provider定义文件
return [
    'think\Request'          => Request::class,
    'think\exception\Handle' => ExceptionHandle::class,
    'model' => Model::class,
    'file' => File::class,
];
