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
                        
                        $v->status = 1;

                        $v->save();

                    }

                }else{

                    $time = strtotime($v->time);

                    if(time() > $time){

                        $v->status = 2;

                        $v->save();

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
            
            

        } catch (\Throwable $th) {
            


        }

    }

}
