<?php

namespace App\Jobs;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendMoney implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

     /**
     * 任务可以尝试的最大次数。
     *
     * @var int
     */
    public $tries = 5;

    /**
     * 任务可以执行的最大秒数 (超时时间)。
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            
            foreach($this->user as $k=>$v){

                $users = \App\User::where('name',$v['name'])->where('card',$v['card'])->first();
                
                if(isset($users['openid'])){
    
                    if($users['money'] != 0){
                        
                        $app = app('wechat.official_account');
    
                        $app->template_message->send([
                            'touser' => $users['openid'],//用户openid
                            'template_id' => 'oSqPEcvbSFuWCKrA8HjkxoyTg0vCqN3YjddIyadRBJY',//发送的模板id
                            'url' => 'http://anlian.mpsjdd.cn/h5/#/',//发送后用户点击跳转的链接
                            'data' => [
                                'first' => '您收到一笔新的支付通知',
                                'keyword1' => mt_rand(10000000,99999999),
                                'keyword2' => '安联乐捐',
                                'keyword3' => $users['money'].'元',
                                'keyword4' => date('Y-m-d H:i:s',time()),
                                'remark' => '请尽快处理'
                            ],
                        ]);
    
                    }
    
                }
    
            }

        } catch (\Throwable $th) {
            
            \App\ErrorLog::create(['title'=>'发送乐捐模板消息','content'=>$th->getMessage()]);

        }
    }
}
