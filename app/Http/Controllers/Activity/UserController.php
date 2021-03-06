<?php

namespace App\Http\Controllers\Activity;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    
    /**
     * 绑定工号
     */
    public function bindCard(Request $request)
    {

        try {
            
            if(!$request->name) return response()->json(['error'=>['message'=>'请填写姓名']]);

            if(!$request->card) return response()->json(['error'=>['message'=>'请填写工号']]);

            $user = $request->openid;
            
            $staff = \App\User::where('name',$request->name)->first();

            if(!$staff) return response()->json(['error'=>['message'=>'只有本公司员工才能绑定！']]);

            $staff->openid = $user;

            $staff->save();

            $user_money = \App\UserTrvel::where('id',$staff->id)->first();

            if(!$user_money) \App\UserTrvel::create(['user_id'=>$staff->id]);

            return response()->json(['success'=>['message'=>'绑定成功','data'=>$staff->id]]);

        } catch (\Throwable $th) {
            
            return response()->json(['error'=>['message'=>'系统错误']]);

        }

    }

    /**
     * 个人信息
     */
    public function userCard(Request $request)
    {

        try {

            $user = $request->openid;
            
            $staff = \App\User::where('openid',$user)->first();
            
            if($staff){

                if(empty($staff['openid'])){

                    $data['id'] = '0';

                    $data['card'] = '';

                    $data['name'] = '';

                    $data['money'] = \App\UserTrvel::where(['user_id'=>$staff->id])->value('money') ?? 0;

                    return response()->json(['success'=>['message'=>'获取成功','data'=>$data]]);
    
                }

            }else{

                $data['id'] = '0';

                $data['card'] = '';

                $data['name'] = '';

                return response()->json(['success'=>['message'=>'获取成功','data'=>$data]]);

            }

            return response()->json(['success'=>['message'=>'获取成功','data'=>$staff]]);

        } catch (\Throwable $th) {
            
            return response()->json(['error'=>['message'=>'系统错误']]);

        }

    }

}
