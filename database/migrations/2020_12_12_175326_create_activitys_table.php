<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateActivitysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activitys', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('title')->nullable()->comment('活动标题');

            $table->string('introduce')->nullable()->comment('活动简介');

            $table->bigInteger('max_people')->nullable()->comment('限制人数');

            $table->string('open_people')->nullable()->comment('活动发起人');

            $table->string('time')->nullable()->comment('活动时间');

            $table->string('address')->nullable()->comment('活动地址');

            $table->longText('desc')->nullable()->comment('活动详情图文');

            $table->bigInteger('status')->default(0)->comment('活动状态 0可预约 1进行中 2已失效');

            $table->timestamps();
        });
        DB::statement("ALTER TABLE `activitys` comment '活动表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activitys');
    }
}
