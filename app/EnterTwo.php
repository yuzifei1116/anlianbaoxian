<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EnterTwo extends Model
{
    /**
     * [$guarded 黑名单设置]
     * @var array
     */
    protected $guarded = [];

    protected $table = "enter_twos";
}
