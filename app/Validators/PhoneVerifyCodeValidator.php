<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

namespace App\Validators;

use App\Services\PhoneVerifyCodeService;
use Illuminate\Support\Facades\Log;

/**
 * 手机短信验证码验证
 *
 * @author Tongle Xu <xutongle@gmail.com>
 */
class PhoneVerifyCodeValidator
{
    public function validate($attribute, $value, $parameters, $validator): bool
    {
        $phone = request($parameters[0] ?? 'verify_phone');

        Log::debug('phone verify: ', [$parameters, $phone]);
        if (empty($phone)) {
            return false;
        }
        $service = PhoneVerifyCodeService::make($phone, request()->getClientIp());
        if (config('app.env') == 'testing') {
            $service->setFixedVerifyCode(123456);
        }
        if ($service->validate($value, false)) {
            return true;
        }
        return false;
    }
}
