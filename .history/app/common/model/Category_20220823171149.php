<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-08-23 13:59:28
 * @LastEditTime: 2022-08-23 17:11:48
 * @FilePath: \web\app\common\model\Category.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

namespace app\common\model;

use think\Model;

class Category extends Model
{
    use \mod\Category;
    //自定义内容
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
