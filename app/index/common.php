<?php

use \think\facade\Route;
//导航
function nav($nav, $key = 0)
{
    $li = '';
    foreach ($nav as $k => $v) {
        if ($v['pid'] == $key) {
            $url = Route::buildUrl('index/category/index', ['category_id' => $v['id']]);
            $li .= '<li><a href="' . $url . '">' . $v['title'] . '</a>';
            if ($children = nav($nav, $v['id'])) {
                $li .= '<ul>';
                $li .= $children;
                $li .= '</ul>';
            }
            $li .= '</li>';
        }
    }
    return $li;
}
