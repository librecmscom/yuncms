<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

namespace App\Validators;

use App\Services\PhoneVerifyCodeService;

/**
 * 验证手机号是否可以获取验证码
 *
 * @codeCoverageIgnore
 * @author Tongle Xu <xutongle@gmail.com>
 */
class PhoneVerifyValidator
{
    public function validate($attribute, $value, $parameters, $validator): bool
    {
        $verifyCode = PhoneVerifyCodeService::make($value, request()->ip());
        if (config('app.env') == 'testing') {
            return true;
        }
        //一个IP地址每天最多发送 20
        if ($verifyCode->getIpSendCount() > settings('sms.ip_count', 20)) {
            return false;
        }
        //一个手机号码每天最多发送 10条
        if ($verifyCode->getPhoneSendCount() > settings('sms.phone_count', 10)) {
            return false;
        }
        return true;
    }
}
