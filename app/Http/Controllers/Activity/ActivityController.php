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
            
            return response()->json(['error'=>['message'=>'系统错误']]);

        }

    }

    /**
     * 活动信息
     */
    public function activityFirst(Request $request)
    {

        try { 
            
            if(!$request->id) return response()->json(['error'=>['message'=>'请选择活动']]);
            
            $data  = \App\Activity::where('id',$request->id)->get();  

            return response()->json(['success'=>['message'=>'获取成功','data'=>$data]]);

        } catch (\Throwable $th) {
            
            return response()->json(['error'=>['message'=>$th->getMessage()]]);

        }

    }

    /**
     * 活动报名
     */
    public function enter_activity(Request $request)
    {

        try {
            
            //本人报名
            if($request->type == 1){

                if(!$request->user_id) return response()->json(['error'=>['message'=>'请先绑定工号']]);

                $user = \App\User::where('id',$request->user_id)->first();

                if(!$user)  return response()->json(['error'=>['message'=>'您非本公司员工']]);
                
                if(!$request->activity_id) return response()->json(['error'=>['message'=>'请选择活动']]);

                \App\Enter::create(['user_id'=>$request->user_id,'activity_id'=>$request->activity_id]);

                $act = \App\Activity::where('id',$request->activity_id)->first();
                
                //发送模板消息
                $users = session('wechat.oauth_user.default');
            
                $app = app('wechat.official_account');

                $app->template_message->send([
                    'touser' => $users->id,//用户openid
                    'template_id' => 'r2JDNj8VULHjaRjRSjq10iuvuyDXzQO46fbCd-f9qC4',//发送的模板id
                    'url' => 'http://finance.chengzhangxiu.com/api/activityList',//发送后用户点击跳转的链接
                    'data' => [
                        'first' => '您好，您已经报名成功!',
                        'keyword1' => $act->title,
                        'keyword2' => date('Y-m-d H:i:s',time()),
                        'keyword3' => $act->address,
                        'remark' => '活动发起成功，请按时参加'
                    ],
                ]);

            //邀约人报名
            }else{

                if(!$request->activity_id) return response()->json(['error'=>['message'=>'请选择活动']]);

                if(!$request->user_id) return response()->json(['error'=>['message'=>'请先绑定工号']]);

                $user = \App\User::where('id',$request->user_id)->first();

                if(!$user)  return response()->json(['error'=>['message'=>'您非本公司员工']]);

                if(!$request->name) return response()->json(['error'=>['message'=>'请填写邀约人姓名']]);

                if(!$request->sex) return response()->json(['error'=>['message'=>'请填写邀约人性别']]);

                if(!$request->old) return response()->json(['error'=>['message'=>'请填写邀约人年龄']]);

                if(!$request->study) return response()->json(['error'=>['message'=>'请填写邀约人学历']]);

                if(!$request->job) return response()->json(['error'=>['message'=>'请填写邀约人职位']]);

                if(!$request->phone) return response()->json(['error'=>['message'=>'请填写邀约人电话']]);

                if(!$request->desc) return response()->json(['error'=>['message'=>'请填写邀约人简介']]);

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

                $act = \App\Activity::where('id',$request->activity_id)->first();

                //发送模板消息
                $users = session('wechat.oauth_user.default');
            
                $app = app('wechat.official_account');

                $app->template_message->send([
                    'touser' => $users->id,//用户openid
                    'template_id' => 'r2JDNj8VULHjaRjRSjq10iuvuyDXzQO46fbCd-f9qC4',//发送的模板id
                    'url' => 'http://finance.chengzhangxiu.com/api/activityList',//发送后用户点击跳转的链接
                    'data' => [
                        'first' => '您好，您已经报名成功!',
                        'keyword1' => $act->title,
                        'keyword2' => date('Y-m-d H:i:s',time()),
                        'keyword3' => $act->address,
                        'remark' => '活动发起成功，请按时参加'
                    ],
                ]);

            }

            return response()->json(['success'=>['message'=>'报名成功','data'=>[]]]);

        } catch (\Throwable $th) {
            
            return response()->json(['error'=>['message'=>'系统错误']]);

        }

    }

    /**
     * 取消报名
     */
    public function cancel_activity(Request $request)
    {

        try {

            if(!$request->id) return response()->json(['error'=>['message'=>'请选择活动']]);
            
            $data  = \App\Enter::where('id',$request->id)->first();

            if(strtotime($data->time) > time()){

                return response()->json(['error'=>['message'=>'活动已过期']]);

            }

            $data  = \App\Enter::where('id',$request->id)->delete();

            //发送模板消息

            return response()->json(['success'=>['message'=>'取消成功','data'=>[]]]);

        } catch (\Throwable $th) {
            
            return response()->json(['error'=>['message'=>'系统错误']]);

        }

    }

    /**
     * 报名记录
     */
    public function act_logs(Request $request)
    {

        try {
            
            if(!$request->user_id) return response()->json(['error'=>['message'=>'请先绑定工号']]);

            $user = \App\User::where('id',$request->user_id)->first();

            if(!$user)  return response()->json(['error'=>['message'=>'您非本公司员工']]);

            $data = \App\Enter::whereOr('user_id',$user->id)->whereOr('invite_user',$user->id)->get();

            foreach($data as $k=>$v){

                if($v['user_id'] != $user->id && $v['invite_user'] != $user->id){

                    unset($data[$k]);

                }

            }

            return response()->json(['success'=>['message'=>'取消成功','data'=>$data]]);

        } catch (\Throwable $th) {
            
            return response()->json(['error'=>['message'=>'系统错误']]);

        }   

    }

}
