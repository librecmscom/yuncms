<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id()->comment('用户ID');
            $table->string('name', 50)->unique()->nullable()->comment('用户名称');
            $table->string('email')->unique()->nullable()->comment('邮箱');
            $table->string('phone', 11)->unique()->nullable()->comment('手机号');
            $table->string('nickname')->nullable()->comment('用户名称');
            $table->string('avatar')->nullable()->comment('头像');
            $table->tinyInteger('auth_status')->nullable()->default(false)->comment('认证状态：0 未提交 1 等待认证 2 认证通过 3 认证失败');
            $table->tinyInteger('status')->default(0)->comment('用户状态：0正常 1禁用');
            $table->unsignedInteger('score')->nullable()->default(0)->comment('积分');

            $table->string('password')->nullable()->comment('密码');
            $table->rememberToken()->comment('记住我token');
            $table->timestamp('phone_verified_at')->nullable()->comment('手机验证时间');
            $table->timestamp('email_verified_at')->nullable()->comment('邮件验证时间');
            $table->timestamps();
            $table->softDeletes()->comment('删除时间');

            $table->comment('用户表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
