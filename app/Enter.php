<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Enter extends Model
{
    /**
     * [$guarded 黑名单设置]
     * @var array
     */
    protected $guarded = [];

    protected $table = "enters";

    /**
     * 报名表反向关联活动表
     */
    public function activity()
    {
        return $this->belongsTo('App\Activity','activity_id','id');
    }
}
