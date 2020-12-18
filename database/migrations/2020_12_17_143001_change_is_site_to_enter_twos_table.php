<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeIsSiteToEnterTwosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('enter_twos', function (Blueprint $table) {
            //
            $table->bigInteger('is_site')->default(0)->comment('状态 0待发送模板消息 1待确定报名 2已过期')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('enter_twos', function (Blueprint $table) {
            //
        });
    }
}
