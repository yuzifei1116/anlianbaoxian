<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateEntersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enters', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('user_id')->nullable()->comment('用户id');

            $table->bigInteger('activity_id')->nullable()->comment('活动id');

            $table->string('name')->nullable()->comment('邀请人姓名');

            $table->bigInteger('sex')->nullable()->comment('邀请人性别');

            $table->bigInteger('old')->nullable()->comment('邀请人年龄');

            $table->string('study')->nullable()->comment('邀请人学历');

            $table->string('job')->nullable()->comment('邀请人职业');

            $table->string('phone')->nullable()->comment('邀请人手机号');

            $table->string('desc')->nullable()->comment('邀请人介绍');

            $table->bigInteger('invite_user')->nullable()->comment('推荐人');

            $table->bigInteger('is_site')->default(0)->comment('是否过期 0未过期 1已过期');

            $table->timestamps();
        });
        DB::statement("ALTER TABLE `enters` comment '报名表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enters');
    }
}
