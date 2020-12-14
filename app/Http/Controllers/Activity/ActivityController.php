<?php

namespace App\Http\Controllers\Activity;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ActivityController extends Controller
{
    
    /**
     * 活动列表
     */
    public function activityList(Request $request)
    {

        try {
            
            $data['book']  = \App\Activity::select('id','title','introduce','max_people','created_at')->where('status',0)->get();

            $data['using'] = \App\Activity::select('id','title','introduce','max_people','created_at')->where('status',1)->get();

            return response()->json(['success'=>['message'=>'获取成功','data'=>$data]]);

        } catch (\Throwable $th) {
            
            return response()->json(['error'=>['message'=>'系统错误,请联系客服']]);

        }

    }

    /**
     * 活动信息
     */
    public function activityFirst(Request $request)
    {

        try {
            
            $app = app('wechat.official_account');
 
            dd($app->user->get($openId));   
            
            $data  = \App\Activity::where('id',$request->id)->get(); 

            return response()->json(['success'=>['message'=>'获取成功','data'=>$data]]);

        } catch (\Throwable $th) {
            
            return response()->json(['error'=>['message'=>'系统错误,请联系客服']]);

        }

    }

    /**
     * 活动报名
     */
    public function enter_activity(Request $request)
    {

        try {
            
            if($request->type == 1){

                \App\Enter::create(['user_id'=>$request->user_id,'activity_id'=>$request->activity_id]);
                
                //发送模板消息

            }else{

                \App\Enter::create([

                    'activity_id'   =>  $request->activity_id,

                    'name'          =>  $request->name,

                    'sex'           =>  $request->sex,

                    'old'           =>  $request->old,

                    'study'         =>  $request->study,

                    'job'           =>  $request->job,

                    'phone'         =>  $request->phone,

                    'desc'          =>  $request->desc,

                    'invite_user'   =>  $request->user_id,

                ]);

                //发送模板消息

            }

            return response()->json(['success'=>['message'=>'报名成功','data'=>[]]]);

        } catch (\Throwable $th) {
            
            return response()->json(['error'=>['message'=>'系统错误,请联系客服']]);

        }

    }

    /**
     * 取消报名
     */
    public function cancel_activity(Request $request)
    {

        try {
            
            $data  = \App\Enter::where('id',$request->id)->delete();

            return response()->json(['success'=>['message'=>'取消成功','data'=>[]]]);

        } catch (\Throwable $th) {
            
            return response()->json(['error'=>['message'=>'系统错误,请联系客服']]);

        }

    }

}
