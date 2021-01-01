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
// 给User控制器设置快捷路由
use think\Route;

Route::any('config','api/config/index');

Route::controller('video','api/video');

Route::controller('live','api/live');

Route::controller('subject','api/subject');

Route::controller('type','api/type');

Route::controller('pay','api/pay');
Route::any('codepay/index','api/codePay/index');
Route::any('codepay/integral','api/codePay/integral');
Route::any('codepay/notify','api/codePay/notify');
Route::any('codepay/notifyIntegral','api/codePay/notifyIntegral');

Route::controller('user','api/user');

Route::controller('skr_comment','api/skrComment');

Route::controller('comment','api/comment');

Route::controller('skr','api/skr');

Route::controller('negative','api/negative');

Route::controller('negative_comment','api/negativeComment');

Route::controller('collection','api/collection');

Route::controller('follow','api/follow');

Route::controller('search','api/searcher');

Route::controller('advert','api/advert');
Route::controller('text_image','api/textImage');

Route::controller('captcha','index/index/captcha');
Route::get('thumb/:scale','api/api/img');
return [];
