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
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id')->index()->nullable()->comment('父地区');
            $table->tinyInteger('level')->comment('地区等级');
            $table->string('name', 50)->index()->comment('地区名称');
            $table->unsignedInteger('city_code')->nullable()->comment('城市区号');
            $table->unsignedInteger('ad_code')->nullable()->comment('区域编码');
            $table->string('lng_lat', 30)->nullable()->comment('中心经纬度');
            $table->smallInteger('order')->default(0)->nullable()->comment('排序');
            $table->timestamps();

            $table->comment('地区表');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('areas');
    }
};
