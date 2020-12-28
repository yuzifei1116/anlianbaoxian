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
            
            \App\ErrorLog::create(['title'=>'改变活动&报名状态','content'=>$th->getMessage()]);

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
                
                if(!isset($v->invite_user)){

                    $user = \App\User::where('id',$v->user_id)->first();
                    
                    $app = app('wechat.official_account');

                    $app->template_message->send([
                        'touser' => $user->openid,//用户openid
                        'template_id' => 'r2JDNj8VULHjaRjRSjq10iuvuyDXzQO46fbCd-f9qC4',//发送的模板id
                        'url' => 'http://anlian.mpsjdd.cn/h5/#/',//发送后用户点击跳转的链接
                        'data' => [
                            'first' => '活动提醒',
                            'keyword1' => $act->title,
                            'keyword2' => date('Y-m-d H:i:s',time()),
                            'keyword3' => $act->address,
                            'remark' => '请按时参加'
                        ],
                    ]);

                }else{

                    $user = \App\User::where('id',$v->invite_user)->first();

                    $app = app('wechat.official_account');

                    $app->template_message->send([
                        'touser' => $user->openid,//用户openid
                        'template_id' => 'r2JDNj8VULHjaRjRSjq10iuvuyDXzQO46fbCd-f9qC4',//发送的模板id
                        'url' => 'http://anlian.mpsjdd.cn/h5/#/',//发送后用户点击跳转的链接
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
            
            \App\ErrorLog::create(['title'=>'每天给报名过的用户发模板消息','content'=>$th->getMessage()]);

        }

    }

    /**
     * 定时任务-给下一个排队中用户发送模板消息
     */
    public function send_activity()
    {

        try {
            
            $data = \App\EnterTwo::where('is_site',1)->get();

            if($data){

                foreach($data as $k=>$v){

                    $v->is_site = 2;

                    $v->save();

                    $first = \App\EnterTwo::where('activity_id',$v->activity_id)->where('is_site',0)->orderBy('id','asc')->first();

                    $activity = \App\Activity::where('id',$v->activity_id)->first();

                    if($first->user_id){

                        $openid = \App\User::where('id',$first->user_id)->value('openid');

                        $app = app('wechat.official_account');

                        $app->template_message->send([
                            'touser' => $openid,//用户openid
                            'template_id' => 'r2JDNj8VULHjaRjRSjq10iuvuyDXzQO46fbCd-f9qC4',//发送的模板id
                            'url' => 'http://anlian.mpsjdd.cn/h5/#/',//发送后用户点击跳转的链接
                            'data' => [
                                'first' => '您已经排队成功!可以报名',
                                'keyword1' => $activity->title,
                                'keyword2' => date('Y-m-d H:i:s',time()),
                                'keyword3' => $activity->address,
                                'remark' => '活动排队成功，请30分钟内报名'
                            ],
                        ]);

                    }

                }

            }

        } catch (\Throwable $th) {
            
            \App\ErrorLog::create(['title'=>'排队成功发模板消息','content'=>$th->getMessage()]);

        }

    }

}
