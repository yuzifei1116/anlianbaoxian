<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserTrvel extends Model
{
    /**
     * [$guarded 黑名单设置]
     * @var array
     */
    protected $guarded = [];

    protected $table = "user_trvel";
}
