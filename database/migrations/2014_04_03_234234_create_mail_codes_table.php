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
        Schema::create('mail_codes', function (Blueprint $table) {
            $table->id()->comment('验证码ID');
            $table->string('email')->index()->comment('邮箱地址');
            $table->string('code', 10)->default('')->comment('验证码');
            $table->tinyInteger('state')->default(0)->comment('验证状态');
            $table->ipAddress('ip')->default('')->comment('ip');
            $table->timestamp('send_at')->nullable();
            $table->timestamp('usage_at')->nullable();

            $table->comment('邮件验证码表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_codes');
    }
};
