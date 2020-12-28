<?php

namespace App\Http\Controllers\Activity;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use EasyWeChat\Factory;

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

            // $user = session('user');

            $user = \Session::get('user');
            
            $user_id = \App\User::where('openid',$user)->value('id');

            if($data['book']){

                foreach($data['book'] as $k=>$v){

                    if(empty($user_id)){
                        
                        $data['book'][$k]['status'] = 0;
        
                    }

                    $act = \App\Appoint::where('activity_id',$v->id)->where('user_id',$user_id)->first();
                    
                    if(!empty($act)){

                        $data['book'][$k]['status'] = 1;

                    }else{

                        $data['book'][$k]['status'] = 0;

                    }

                }

            }
            
            if($data['using']){

                foreach($data['using'] as $k=>$v){

                    if(empty($user_id)){

                        $data['using'][$k]['status'] = 0;

                    }
                    
                    $act = \App\Enter::where('activity_id',$v->id)->where('user_id',$user_id)->first();

                    $acts = \App\EnterTwo::where('activity_id',$v->id)->where('user_id',$user_id)->first();
                    
                    if(!empty($act) || !empty($acts)){

                        $data['using'][$k]['status'] = 1;

                    }else{

                        $data['using'][$k]['status'] = 0;

                    }

                }

            }
            
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
            
            return response()->json(['error'=>['message'=>'系统错误']]);

        }

    }

    /**
     * 活动预约
     */
    public function enters_activity(Request $request)
    {

        try {
            //本人预约
            if($request->type == 1){
                
                if(!$request->user_id) return response()->json(['error'=>['message'=>'请先绑定工号']]);

                $user = \App\User::where('id',$request->user_id)->first();

                if(!$user)  return response()->json(['error'=>['message'=>'您非本公司员工']]);
                
                if(!$request->activity_id) return response()->json(['error'=>['message'=>'请选择活动']]);

                $act = \App\Activity::where('id',$request->activity_id)->first();
                
                $user_act = \App\Appoint::where('user_id',$request->user_id)->where('activity_id',$request->activity_id)->first();
                
                if($user_act) return response()->json(['error'=>['message'=>'您已经预约过了']]);

                \App\Appoint::create(['user_id'=>$request->user_id,'activity_id'=>$request->activity_id]);

            //邀约人预约
            }else{
                
                if(!$request->activity_id) return response()->json(['error'=>['message'=>'请选择活动']]);

                if(!$request->user_id) return response()->json(['error'=>['message'=>'请先绑定工号']]);

                $user = \App\User::where('id',$request->user_id)->first();

                if(!$user)  return response()->json(['error'=>['message'=>'您非本公司员工']]);

                if(!$request->name) return response()->json(['error'=>['message'=>'请填写邀约人姓名']]);

                if(!$request->sex)  return response()->json(['error'=>['message'=>'请填写邀约人性别']]);

                if(!$request->old) return response()->json(['error'=>['message'=>'请填写邀约人年龄']]);

                if(!$request->study) return response()->json(['error'=>['message'=>'请填写邀约人学历']]);

                if(!$request->job) return response()->json(['error'=>['message'=>'请填写邀约人职位']]);

                if(!$request->phone) return response()->json(['error'=>['message'=>'请填写邀约人电话']]);

                if(!$request->desc) return response()->json(['error'=>['message'=>'请填写邀约人简介']]);

                $act = \App\Activity::where('id',$request->activity_id)->first();

                $user_act = \App\Appoint::where('name',$request->name)->where('activity_id',$request->activity_id)->first();

                if($user_act) return response()->json(['error'=>['message'=>'您已经预约过了']]);

                \App\Appoint::create([

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

            }

            return response()->json(['success'=>['message'=>'预约成功','data'=>[]]]);

        } catch (\Throwable $th) {
            
            return response()->json(['error'=>['message'=>'系统错误']]);

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
                
                if(!$request->user_id || $request->user_id == '') return response()->json(['error'=>['message'=>'请先绑定工号']]);

                if(!$request->activity_id) return response()->json(['error'=>['message'=>'请选择活动']]);

                $enters = \App\EnterTwo::where('activity_id',$request->activity_id)->where('user_id',$request->user_id)->orderBy('id','asc')->first();

                if($enters){
                    
                    if($enters->is_site == 0) return response()->json(['error'=>['message'=>'您正在排队中']]);

                    if($enters->is_site == 2) return response()->json(['error'=>['message'=>'已过期,不可以报名']]);

                    if($enters->is_site == 1){

                        $enters->is_site = 2;

                        $enters->save();

                    }

                }
                
                $user = \App\User::where('id',$request->user_id)->first();

                if(!$user)  return response()->json(['error'=>['message'=>'您非本公司员工']]);
                
                $act = \App\Activity::where('id',$request->activity_id)->first();
                
                $people = \App\Enter::where('activity_id',$request->activity_id)->count();
                
                $user_act = \App\Enter::where('user_id',$request->user_id)->where('activity_id',$request->activity_id)->first();
                
                $user_act_two = \App\EnterTwo::where('user_id',$request->user_id)->where('activity_id',$request->activity_id)->first();
                
                if($user_act || $user_act_two) return response()->json(['error'=>['message'=>'您已经报过名了']]);

                if($act->max_people == $people){

                    \App\EnterTwo::create(['user_id'=>$request->user_id,'activity_id'=>$request->activity_id]);

                    return response()->json(['success'=>['message'=>'报名排队中...','data'=>[]]]);

                }

                \App\Enter::create(['user_id'=>$request->user_id,'activity_id'=>$request->activity_id]);
                
                //发送模板消息
                
                $users = session('user');
            
                $app = app('wechat.official_account');

                $app->template_message->send([
                    'touser' => $users,//用户openid
                    'template_id' => 'r2JDNj8VULHjaRjRSjq10iuvuyDXzQO46fbCd-f9qC4',//发送的模板id
                    'url' => 'http://anlian.mpsjdd.cn/h5/#/',//发送后用户点击跳转的链接
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

                $entersEs = \App\EnterTwo::where('activity_id',$request->activity_id)->where('invite_user',$request->user_id)->orderBy('id','asc')->first();

                if($entersEs){

                    if($entersEs->is_site == 0) return response()->json(['error'=>['message'=>'您正在排队中']]);

                    if($entersEs->is_site == 2) return response()->json(['error'=>['message'=>'已过期,不可以报名']]);

                    if($entersEs->is_site == 1){

                        $entersEs->is_site = 2;

                        $entersEs->save();

                    }

                }

                $user = \App\User::where('id',$request->user_id)->first();

                if(!$user)  return response()->json(['error'=>['message'=>'您非本公司员工']]);

                if(!$request->name) return response()->json(['error'=>['message'=>'请填写邀约人姓名']]);

                if(!$request->sex)  return response()->json(['error'=>['message'=>'请填写邀约人性别']]);

                if(!$request->old) return response()->json(['error'=>['message'=>'请填写邀约人年龄']]);

                if(!$request->study) return response()->json(['error'=>['message'=>'请填写邀约人学历']]);

                if(!$request->job) return response()->json(['error'=>['message'=>'请填写邀约人职位']]);

                if(!$request->phone) return response()->json(['error'=>['message'=>'请填写邀约人电话']]);

                if(!$request->desc) return response()->json(['error'=>['message'=>'请填写邀约人简介']]);

                $act = \App\Activity::where('id',$request->activity_id)->first();

                $people = \App\Enter::where('activity_id',$request->activity_id)->count();

                $user_act = \App\Enter::where('name',$request->name)->where('activity_id',$request->activity_id)->first();

                $user_act_two = \App\EnterTwo::where('name',$request->name)->where('activity_id',$request->activity_id)->first();

                if($user_act || $user_act_two) return response()->json(['error'=>['message'=>'您已经报过名了']]);

                if($act->max_people == $people){

                    \App\EnterTwo::create([

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

                    return response()->json(['success'=>['message'=>'报名排队中...','data'=>[]]]);

                }

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
                $users = session('user');
            
                $app = app('wechat.official_account');

                $app->template_message->send([
                    'touser' => $users,//用户openid
                    'template_id' => 'oSqPEcvbSFuWCKrA8HjkxoyTg0vCqN3YjddIyadRBJY',//发送的模板id
                    'url' => 'http://anlian.mpsjdd.cn/h5/#/',//发送后用户点击跳转的链接
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
            
            $activity = \App\Activity::where('id',$data->activity_id)->first();
            
            if(strtotime($activity->time) < time()){

                return response()->json(['error'=>['message'=>'活动已过期']]);

            }

            $people = \App\Enter::where('activity_id',$data->activity_id)->count();
            
            if($activity->max_people == $people){
                
                //发送模板消息
                $enters = \App\EnterTwo::where('activity_id',$activity->id)->orderBy('id','asc')->where('is_site',0)->first();
                
                if($enters){

                    if(isset($enters->user_id)){

                        $user_id = $enters->user_id;

                    }else{

                        $user_id = $enters->invite_user;

                    }
                    
                    $enters->is_site = 1;

                    $enters->save();

                    $users = \App\User::where('id',$user_id)->first();
                
                    if($users){

                        $app = app('wechat.official_account');

                        $app->template_message->send([
                            'touser' => $users->openid,//用户openid
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
                
                $data  = \App\Enter::where('id',$request->id)->delete();
 
            }else{
                
                $data  = \App\Enter::where('id',$request->id)->delete();

            }

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

            // $user = session('user');

            // $user_ids = \App\User::where('openid',$user)->first();
            
            // if(!$user_ids) return response()->json(['error'=>['message'=>'请先绑定工号']]);

            // 

            // if(!$users)  return response()->json(['error'=>['message'=>'请先绑定工号']]);
            
            if(!$request->user_id || $request->user_id == '0'){

                return response()->json(['error'=>['message'=>'请先绑定工号']]);

            }  

            $user = session('user');

            $users = \App\User::where('id',$request->user_id)->first();

            $user_id = \App\User::where('openid',$user)->value('id');

            //预约记录
            $data['book'] = \App\Appoint::whereOr('user_id',$users->id)->whereOr('invite_user',$users->id)->get();

            if($data['book']){

                foreach($data['book'] as $k=>$v){

                    if($v['user_id'] != $user_id && $v['invite_user'] != $user_id){
                        
                        unset($data['book'][$k]);
    
                    }
    
                }

            }
            
            if($data['book']){

                foreach($data['book'] as $k=>$v){

                    $act = \App\Activity::where('id',$v->activity_id)->first();

                    $actss = \App\Enter::where('activity_id',$v->activity_id)->where('user_id',$user_id)->first();

                    $acts = \App\EnterTwo::where('activity_id',$v->activity_id)->where('user_id',$user_id)->first();
                    
                    if($act->status == 0 || $act->status == 2 || !empty($acts) || !empty($actss) ){
                        
                        $data['book'][$k]['status'] = 1;

                    }else{

                        $data['book'][$k]['status'] = 0;

                    }

                }

                foreach($data['book'] as $k=>$v){

                    $data['book'][$k]['title'] = $v->activity->title;
    
                    $data['book'][$k]['introduce'] = $v->activity->introduce;
    
                    $data['book'][$k]['max_people'] = $v->activity->max_people;

                    unset($data['book'][$k]['activity']);

                }

            }
            
            //报名记录
            $data['using'] = \App\Enter::whereOr('user_id',$users->id)->whereOr('invite_user',$users->id)->get();
            
            if($data['using']){
                
                foreach($data['using'] as $k=>$v){
                    
                    if($v['user_id'] != $users->id && $v['invite_user'] != $users->id){
    
                        unset($data['using'][$k]);
    
                    }
                    
                    if(strtotime($v['created_at']) + 7200 < time()){

                        unset($data['using'][$k]);

                    }
    
                }

            }
            
            if($data['using']){

                foreach($data['using'] as $k=>$v){

                    $data['using'][$k]['title'] = $v->activity->title;
    
                    $data['using'][$k]['introduce'] = $v->activity->introduce;
    
                    $data['using'][$k]['max_people'] = $v->activity->max_people;

                    unset($data['using'][$k]['activity']);

                }

            }
            
            return response()->json(['success'=>['message'=>'获取成功','data'=>$data]]);

        } catch (\Throwable $th) {
            
            return response()->json(['error'=>['message'=>'系统错误']]);

        }   

    }

    /**
     * 签到
     */
    public function sign(Request $request)
    {

        try {
            
            if(!$request->id) return response()->json(['error'=>['message'=>'请选择活动']]);

            $data = \App\Enter::where('id',$request->id)->first();

            $act = \App\Activity::where('id',$data->activity_id)->first();

            if(time() < strtotime($act->time)) return response()->json(['error'=>['message'=>'未到签到时间']]);

            if($data->sign == 1){

                return response()->json(['error'=>['message'=>'您已经签到过']]);

            }else{

                $data->sign = 1;

                $data->save();

            }      

            return response()->json(['success'=>['message'=>'签到成功','data'=>[]]]);

        } catch (\Throwable $th) {
            
            return response()->json(['error'=>['message'=>'系统错误']]);

        }

    }

}
