<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

//后台接口域名路由 adminapi
//Route::domain('adminapi', function(){
//    //adminapi模块首页路由
//    Route::get('/', 'adminapi/index/index');
//    //定义 域名下的其他路由
//    //比如 以后定义路由 get请求  http://adminapi.pyg.com/goods  访问到 adminapi模块Goods控制器index方法
//    //Route::resource('goods', 'adminapi/goods');
//    //获取验证码接口
//    Route::get('captcha/:id', "\\think\\captcha\\CaptchaController@index");
//    Route::get('captcha', 'adminapi/login/captcha');
//    //登录接口
//    Route::post('login', 'adminapi/login/login');
//    //退出接口
//    Route::get('logout', 'adminapi/login/logout');
//    //权限接口
//    Route::resource('auths', 'adminapi/auth', [], ['id'=>'\d+']);
//    //查询菜单权限的接口
//    Route::get('nav', 'adminapi/auth/nav');
//    //角色接口
//    Route::resource('roles', 'adminapi/role', [], ['id'=>'\d+']);
//    //管理员接口
//    Route::resource('admins', 'adminapi/admin', [], ['id'=>'\d+']);
//    //商品分类接口
//    Route::resource('categorys', 'adminapi/category', [], ['id'=>'\d+']);
//
//    //单图片上传接口
//    Route::post('logo', 'adminapi/upload/logo');
//    //多图片上传接口
//    Route::post('images','adminapi/upload/images');
//    //商品品牌接口
//    Route::resource('brands','adminapi/brand',[],['id'=>'\d+']);
//    //商品模块（类型）接口
//    Route::resource('types','adminapi/type',[],['id'=>'\d+']);
//});

Route::get('index/captcha','index/login/captcha');
Route::get('index/captchas','index/user/captcha');
Route::post('index/login','index/login/login');
Route::post('index/register','index/user/save');
Route::post('index/userinfo','index/user/index');
Route::post('index/userinfo/update','index/user/edit');
Route::get('index/logout','index/login/logout');
Route::post('index/tokens','index/user/index');
Route::post('index/addmeuns','index/auth/save');
Route::get('index/meunslist','index/auth/nav');
Route::post('index/updatemeuns','index/auth/update');
Route::post('index/deletemeuns','index/auth/delete');
Route::post('index/uploadfile','index/upload/logo');
Route::post('index/addPosts','index/post/save');

Route::get('index/postlist','index/post/index');
Route::any('index/postinfo/:id','index/post/read',['method'=>'get']);





Route::get('admin/captcha','adminapi/login/captcha');
Route::get('admin/captchas','adminapi/user/captcha');
Route::post('admin/login','adminapi/login/login');
Route::post('admin/register','adminapi/user/save');
Route::post('admin/userinfo','adminapi/user/index');
Route::post('admin/userinfo/update','adminapi/user/edit');
Route::get('admin/logout','adminapi/login/logout');
Route::post('admin/tokens','adminapi/user/index');
Route::post('admin/addmeuns','adminapi/auth/save');
Route::get('admin/meunslist','adminapi/auth/nav');
Route::post('admin/updatemeuns','adminapi/auth/update');
Route::post('admin/deletemeuns','adminapi/auth/delete');
Route::post('admin/uploadfile','adminapi/upload/logo');
Route::post('admin/addPosts','adminapi/post/save');

Route::get('admin/postlist','adminapi/post/index');
Route::any('admin/postinfo/:id','adminapi/post/read',['method'=>'get']);
Route::post('admin/approval','adminapi/post/approvalpost');


Route::post('admin/adminList','adminapi/admin/index');

Route::post('admin/userList','adminapi/user/lists');
