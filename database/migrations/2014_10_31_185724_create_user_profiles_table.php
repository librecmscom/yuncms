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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->primary()->comment('用户ID');
            $table->unsignedTinyInteger('gender')->nullable()->default(0)->comment('用户性别：0 未知 1 男性 2 女性');
            $table->date('birthday')->nullable()->comment('生日');
            $table->string('website')->nullable()->comment('个人网站');
            $table->string('intro')->nullable()->comment('个人简介');
            $table->text('bio')->nullable()->comment('个性签名');

            $table->comment('用户资料表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_profiles');
    }
};
