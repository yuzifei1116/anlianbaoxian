<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('welcome');
});

Route::get('activity_status','TaskController@activity_status');//定时任务-修改活动状态

Route::any('/servers', 'WeChatController@valid');

Route::any('/wechat', 'WeChatController@serve')->middleware('wechat.oauth');

Route::group(['middleware' => ['web', 'wechat.oauth']], function () {
    Route::get('/user', function () {
        $user = session('wechat.oauth_user.default'); // 拿到授权用户资料

        dd($user);
    });

    Route::get('api/activityList','Activity\ActivityController@activityList'); //活动列表

    Route::get('api/activityFirst','Activity\ActivityController@activityFirst'); //活动详情

    Route::get('api/enter_activity','Activity\ActivityController@enter_activity'); //活动报名

    Route::get('api/cancel_activity','Activity\ActivityController@cancel_activity'); //活动报名

});

