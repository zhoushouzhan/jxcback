<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-21 09:57:50
 * @LastEditTime: 2022-07-24 13:04:39
 * @FilePath: \web\app\AppService.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

declare(strict_types=1);

namespace app;

use think\Service;

/**
 * 应用服务类
 */
class AppService extends Service
{
    public function register()
    {
        // 服务注册
        $path = YP_ROOT . '../extend/user/';
        $files = scandir($path);
        foreach ($files as $v) {
            if (is_file($path . $v)) {
                $modName =  pathinfo($path . $v, PATHINFO_FILENAME);
                $className = '\\user\\' . $modName;
                $this->app->bind($modName, $className);
            }
        }
    }

    public function boot()
    {
        // 服务启动
    }
}
