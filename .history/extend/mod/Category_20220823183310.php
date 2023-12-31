<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-08-23 13:59:28
 * @LastEditTime: 2022-08-23 18:33:09
 * @FilePath: \web\extend\mod\Category.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

namespace mod;

trait Category
{


    //下拉菜单
    public static function getSelected($pid, $ids)
    {
        $data = self::whereRaw("FIND_IN_SET($pid,path)")->select();

        $ids = explode(',', $pid . ',' . $ids);
        $selectNode = [];
        foreach ($ids as $id) {
            $item = [];
            foreach ($data as $k => $v) {
                if ($v['pid'] == $id) {
                    $item[] = $v;
                }
            }
            if ($item)
                $selectNode[] = $item;
        }
        return $selectNode;
    }
}
