<?php

namespace App\Http\Controllers\Activity;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    /**
     * 统一下单--微信支付
     */
    public function order(Request $request)
    {

        try {
            
            $users = $request->openid;

            // $result = $app->order->unify([
            //     'body' => '龙之鹰爱心社-乐捐',
            //     'out_trade_no' => '20150806125346',
            //     'total_fee' => 88,
            //     'spbill_create_ip' => '123.12.12.123', // 可选，如不传该参数，SDK 将会自动获取相应 IP 地址
            //     'notify_url' => 'https://pay.weixin.qq.com/wxpay/pay.action', // 支付结果通知网址，如果不设置则会使用配置里的默认地址
            //     'trade_type' => 'JSAPI', // 请对应换成你的支付方式对应的值类型
            //     'openid' => 'oUpF8uMuAJO_M2pxb1Q9zNjWeS6o',
            // ]);
            

        } catch (\Throwable $th) {
            //throw $th;
        }

    }
}
