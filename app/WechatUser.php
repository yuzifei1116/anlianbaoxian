<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WechatUser extends Model
{
    //
    /**
     * [$guarded 黑名单设置]
     * @var array
     */
    protected $guarded = [];

    protected $table = "wechat_users";
}
