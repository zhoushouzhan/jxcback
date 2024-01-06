<?php
/*
 * @Author: 一品网络技术有限公司
 * @Date: 2022-06-21 09:57:50
 * @LastEditTime: 2022-09-26 20:23:45
 * @FilePath: \web\app\common.php
 * @Description:
 * 联系QQ:58055648
 * Copyright (c) 2022 by 东海县一品网络技术有限公司, All Rights Reserved.
 */

//create tree
function generateTree($data,$pid=0){
    $arr=[];
    $tree=[];
    foreach($data as $v){
        $arr[$v['id']]=$v;
    }
    foreach($arr as $k=>$v){
        if($v['pid']==$pid){
            $tree[]=&$arr[$k];
        }else{
            $arr[$v['pid']]['children'][]=&$arr[$k];
        }
    }
    return $tree;
}

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
            $v['meta'] = $v;
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
//取得随机数
function makeStr($length)
{
    $chars = array(
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
        'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's',
        't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D',
        'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O',
        'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
        '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '!',
        '@', '#', '$', '%', '^', '&', '*', '(', ')', '-', '_',
        '[', ']', '{', '}', '<', '>', '~', '`', '+', '=', ',',
        '.', ';', ':', '/', '?', '|'
    );
    $keys = array_rand($chars, $length);
    $str = '';
    for ($i = 0; $i < $length; $i++) {
        $str .= $chars[$keys[$i]];
    }
    return $str;
}