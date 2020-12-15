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

            $user = session('wechat.oauth_user.default');//获取微信用户信息

            $staff = \App\User::where('name',$request->name)->first();

            if(!$staff) return response()->json(['error'=>['message'=>'只有本公司员工才能绑定！']]);

            $staff->openid = $user->id;

            $staff->save();

            return response()->json(['success'=>['message'=>'绑定成功','data'=>$user->id]]);

        } catch (\Throwable $th) {
            
            return response()->json(['error'=>['message'=>'系统错误']]);

        }

    }

}
