<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * @var array 验证器
     */
    protected array $validators = [
        'domain' => \App\Validators\DomainValidator::class,//域名验证
        'hash' => \App\Validators\HashValidator::class,//Hash
        'id_card' => \App\Validators\IdCardValidator::class,//中国大陆身份证验证
        'latitude' => \App\Validators\LatitudeValidator::class,//经度
        'longitude' => \App\Validators\LongitudeValidator::class,//纬度
        'mail_verify_code' => \App\Validators\MailVerifyCodeValidator::class,//邮件验证码
        'phone' => \App\Validators\PhoneValidator::class,//手机号码
        'phone_verify' => \App\Validators\PhoneVerifyValidator::class,//是否可获取手机验证码
        'phone_verify_code' => \App\Validators\PhoneVerifyCodeValidator::class,//检查手机验证码
        'tel' => \App\Validators\TelPhoneValidator::class,//电话
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //忽略 Passport 默认迁移
        \Laravel\Passport\Passport::ignoreMigrations();
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        \Illuminate\Http\Resources\Json\JsonResource::withoutWrapping();
        \Illuminate\Support\Carbon::setLocale('zh');
        $this->registerValidators();
    }

    /**
     * Register validators.
     */
    protected function registerValidators()
    {
        foreach ($this->validators as $rule => $validator) {
            \Illuminate\Support\Facades\Validator::extend($rule, "{$validator}@validate");
        }
    }
}
