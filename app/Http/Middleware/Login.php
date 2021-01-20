<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use EasyWeChat\Factory;

class Login
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        //微信授权
        $uid = $request->uid;

        if(!$uid){
            
            $appid = 'wxf0ad8be2322aba00';

            $secret = '3476cc4c1b0a0535a275445d2f4fc118';

            $code = 0;

            //获取code
            $refer = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'No refer found';
            
            if(\strlen($refer) > 70 ){

                $refer = substr($refer,33);

                $code = rtrim($refer,'&state=1');

                //取得openid
                $oauth2Url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code=$code&grant_type=authorization_code";

                $oauth2 = $this->getJson($oauth2Url);
                          
                if(array_key_exists('openid',$oauth2)){
                    $openid = $oauth2['openid'];
                    $user = \App\WechatUser::where('open_id',$openid)->first();
                    if($user) $User = \App\WechatUser::create(['open_id',$openid]);
                    $request->openid = $User['openid'];
                }

            }else{

                $redirect_uri = urlencode ('http://anlian.mpsjdd.cn/h5/#/');

                //微信网页授权
                $url ="https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$redirect_uri&response_type=code&scope=snsapi_userinfo&state=1&connect_redirect=1#wechat_redirect";
                
                return response()->json(['success'=>['message'=>'!','data'=>$url,'code'=>201]]);

            }

        }else{

            $User = \App\WechatUser::where('id',$uid)->value('open_id');
            $request->openid = $User['openid'];

        }
        
        return $next($request);
    }

    public function getJson($url){

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $output = curl_exec($ch);

        curl_close($ch);

        return json_decode($output, true);

    }
}
