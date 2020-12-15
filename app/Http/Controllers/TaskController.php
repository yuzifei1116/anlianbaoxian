<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskController extends Controller
{
    
    /**
     * 定时任务-改变活动状态&&报名状态
     */
    public function activity_status()
    {

        try {
            
            $data = \App\Activity::where('status','<>',2)->get();

            foreach($data as $K=>$v){

                if($v->status == 0){

                    $time = strtotime(date('Y-m-d',strtotime($v->time)));

                    $now_time = strtotime(date('Y-m-d',time()));
                    
                    if($time - $now_time < 86400){
                        
                        //修改活动状态
                        $v->status = 1;

                        $v->save();

                    }

                }else{

                    $time = strtotime($v->time);

                    if(time() > $time){

                        //修改活动状态
                        $v->status = 2;

                        $v->save();

                        //修改报名状态
                        \App\Enter::where('activity_id',$v->id)->update(['is_site'=>1]);

                    }

                }

            }

        } catch (\Throwable $th) {
            


        }

    }

    /**
     * 定时任务-每天给报名的员工发送模板消息
     */
    public function send()
    {

        try {
            
            $data = \App\Enter::where('is_site',0)->get();

            foreach($data as $k=>$v){

                $act = \App\Activity::where('id',$v->activity_id)->first();

                if(isset($act->invite_user)){

                    $user = \App\User::where('id',$act->invite_user)->first();

                    $app = app('wechat.official_account');

                    $app->template_message->send([
                        'touser' => $user->openid,//用户openid
                        'template_id' => 'r2JDNj8VULHjaRjRSjq10iuvuyDXzQO46fbCd-f9qC4',//发送的模板id
                        'url' => 'http://finance.chengzhangxiu.com/api/activityList',//发送后用户点击跳转的链接
                        'data' => [
                            'first' => '活动提醒',
                            'keyword1' => $act->title,
                            'keyword2' => date('Y-m-d H:i:s',time()),
                            'keyword3' => $act->address,
                            'remark' => '请按时参加'
                        ],
                    ]);

                }else{

                    $user = \App\User::where('id',$act->user_id)->first();

                    $app = app('wechat.official_account');

                    $app->template_message->send([
                        'touser' => $user->openid,//用户openid
                        'template_id' => 'r2JDNj8VULHjaRjRSjq10iuvuyDXzQO46fbCd-f9qC4',//发送的模板id
                        'url' => 'http://finance.chengzhangxiu.com/api/activityList',//发送后用户点击跳转的链接
                        'data' => [
                            'first' => '活动提醒',
                            'keyword1' => $act->title,
                            'keyword2' => date('Y-m-d H:i:s',time()),
                            'keyword3' => $act->address,
                            'remark' => '请按时参加'
                        ],
                    ]);

                }
 
            }

        } catch (\Throwable $th) {
            


        }

    }

}
