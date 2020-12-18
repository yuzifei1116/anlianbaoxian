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

            $user = session('user');

            $staff = \App\User::where('name',$request->name)->first();

            if(!$staff) return response()->json(['error'=>['message'=>'只有本公司员工才能绑定！']]);

            $staff->openid = $user;

            $staff->save();

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

            $user = session('user');
            
            $staff = \App\User::where('openid',$user)->first();
 
            if(!$staff){

                $staff['name'] = '';

                $staff['card'] = '';

                $staff['openid'] = '';

            }

            return response()->json(['success'=>['message'=>'获取成功','data'=>$staff]]);

        } catch (\Throwable $th) {
            
            return response()->json(['error'=>['message'=>$th->getMessage()]]);

        }

    }

}
