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
    // return view('welcome');
    dd('request');
}); 

Route::get('activity_status','TaskController@activity_status');//定时任务-修改活动状态

Route::get('send','TaskController@send');//定时任务-每天给报名的员工发送模板消息

Route::get('send_activity','TaskController@send_activity');//定时任务-给下一个排队中用户发送模板消息

Route::any('/servers', 'WeChatController@valid');

// Route::any('/wechat', 'WeChatController@serve')->middleware('wechat.oauth');

/**
 * 中间件授权
 */
Route::group(['middleware' => ['web','login']], function () {

    Route::any('api/bindCard','Activity\UserController@bindCard'); //绑定工号

    Route::any('api/act_logs','Activity\ActivityController@act_logs'); //报名记录

    Route::any('api/enters_activity','Activity\ActivityController@enters_activity'); //活动报名

    Route::any('api/enter_activity','Activity\ActivityController@enter_activity'); //活动报名

    Route::any('api/userCard','Activity\UserController@userCard'); //个人信息

    Route::any('api/cancel_activity','Activity\ActivityController@cancel_activity'); //取消报名--

    Route::any('api/activityList','Activity\ActivityController@activityList'); //活动列表

    Route::any('api/activityFirst','Activity\ActivityController@activityFirst'); //活动详情

});

/**
 * 中间件授权
 */
// Route::group(['middleware' => ['web', 'wechat.oauth']], function () {

//     Route::get('/user', function () {
//         $user = session('wechat.oauth_user.default'); // 拿到授权用户资料

//         dd($user);
//     });

//     Route::any('api/bindCard','Activity\UserController@bindCard'); //绑定工号

//     Route::any('api/act_logs','Activity\ActivityController@act_logs'); //报名记录

//     Route::any('api/enters_activity','Activity\ActivityController@enters_activity'); //活动报名

//     Route::any('api/enter_activity','Activity\ActivityController@enter_activity'); //活动报名

//     Route::any('api/userCard','Activity\UserController@userCard'); //个人信息

//     Route::any('api/cancel_activity','Activity\ActivityController@cancel_activity'); //取消报名--

//     Route::any('api/activityList','Activity\ActivityController@activityList'); //活动列表

//     Route::any('api/activityFirst','Activity\ActivityController@activityFirst'); //活动详情

// });
