<?php

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
