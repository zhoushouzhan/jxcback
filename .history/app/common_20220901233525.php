<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-21 09:57:50
 * @LastEditTime: 2022-09-01 23:35:25
 * @FilePath: \web\app\common.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */
//节点层级划分
function layoutData($dataList, $key = 0)
{
    $tree = [];
    foreach ($dataList as $k => $v) {
        if ($v['pid'] == $key) {
            $children = layoutData($dataList, $v['id']);
            if ($children) {
                $v['children'] = $children;
            }
            $v['meta'] = [
                'title' => $v['title'],
                'icon' => $v['icon'],
                'mod' => $v['mod']
            ];
            unset($v['title']);
            unset($v['icon']);
            $tree[] = $v;
        }
    }
    return $tree;
}
//栏目层级划分
function layoutCategory($dataList, $key = 0)
{
    $tree = [];
    foreach ($dataList as $k => $v) {
        if ($v['pid'] == $key) {
            $children = layoutCategory($dataList, $v['id']);
            if ($children) {
                $v['children'] = $children;
            }
            $tree[] = $v;
        }
    }
    return $tree;
}
//格式化size显示
function formatSize($b, $times = 0)
{
    if ($b > 1024) {
        $temp = $b / 1024;
        return formatSize($temp, $times + 1);
    } else {
        $unit = 'B';
        switch ($times) {
            case '0':
                $unit = 'B';
                break;
            case '1':
                $unit = 'KB';
                break;
            case '2':
                $unit = 'MB';
                break;
            case '3':
                $unit = 'GB';
                break;
            case '4':
                $unit = 'TB';
                break;
            case '5':
                $unit = 'PB';
                break;
            case '6':
                $unit = 'EB';
                break;
            case '7':
                $unit = 'ZB';
                break;
            default:
                $unit = '单位未知';
        }
        return sprintf('%.2f', $b) . $unit;
    }
}
