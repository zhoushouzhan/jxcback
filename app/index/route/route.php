<?php

declare(strict_types=1);

use think\facade\Route;


Route::get('index', 'index/index/index');

//栏目规则
Route::get('list-:category_id$', 'index/category/index');
//文章规则
Route::get('article-:id$', 'index/article/index');

//注册
Route::get('register', 'index/member/register');
//注册验证
Route::post('register', 'index/member/register')->validate(\app\index\validate\Member::class, 'register');


//登录
Route::get('login', 'index/member/login');
Route::post('login', 'index/member/login')->validate(\app\index\validate\Member::class, 'login');
//会员中心
Route::get('MemberCenter', 'index/member/index');
//编辑资料
Route::get('MemberEdit', 'index/member/edit');
Route::post('MemberEdit', 'index/member/edit')->validate(\app\index\validate\Member::class, 'edit');
//文章管理
Route::get('MemberArticle', 'index/member/article');
Route::post('MemberArticle', 'index/member/article')->validate(\app\index\validate\Article::class);
