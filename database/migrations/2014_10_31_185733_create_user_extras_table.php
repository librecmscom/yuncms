<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_extras', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary()->comment('用户ID');


            $table->timestamp('last_login_at')->nullable()->comment('最后登录时间');
            $table->ipAddress('last_login_ip')->nullable()->comment('最后登录IP地址');
            $table->unsignedBigInteger('login_num')->nullable()->default(0)->comment('登录次数');
            $table->comment('用户扩展信息表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_extras');
    }
};
