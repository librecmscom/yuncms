<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/**
 * RESTFul API version 1.
 *
 * Define the version of the interface that conforms to most of the
 * REST ful specification.
 */
Route::group(['prefix' => 'v1', 'as' => 'api.v1.'], function (Illuminate\Contracts\Routing\Registrar $api) {

    /**
     * 公共接口
     */
    Route::group(['prefix' => 'common'], function (Illuminate\Contracts\Routing\Registrar $api) {
        $api->post('phone-verify-code', [App\Http\Controllers\Api\V1\CommonController::class, 'phoneVerifyCode'])->name('common.phone-verify-code');//短信验证码
        $api->post('mail-verify-code', [App\Http\Controllers\Api\V1\CommonController::class, 'mailVerifyCode'])->name('common.mail-verify-code');//邮件验证码
        $api->get('country', [App\Http\Controllers\Api\V1\CommonController::class, 'country'])->name('common.country');//国家列表
        $api->get('areas', [App\Http\Controllers\Api\V1\CommonController::class, 'areas'])->name('common.areas');//地区列表
    });

    /**
     * 认证授权
     */
    Route::group(['prefix' => 'auth'], function (Illuminate\Contracts\Routing\Registrar $api) {
    });

    /**
     * 用户接口
     */
    Route::group(['prefix' => 'user'], function () {
    });

    /**
     * 通知
     */
    Route::group(['prefix' => 'notifications'], function () {
    });
});
