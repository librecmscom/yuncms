<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

namespace App\Validators;

use App\Services\MailVerifyCodeService;
use Illuminate\Support\Facades\Log;

/**
 * 邮箱验证码
 *
 * @author Tongle Xu <xutongle@gmail.com>
 */
class MailVerifyCodeValidator
{
    public function validate($attribute, $value, $parameters, $validator): bool
    {
        $email = request($parameters[0] ?? 'verify_email');
        Log::debug('email verify: ', [$parameters, $email]);
        if (empty($email)) {
            return false;
        }
        $service = MailVerifyCodeService::make($email, request()->ip());
        if (config('app.env') == 'testing') {
            $service->setFixedVerifyCode(123456);
        }
        if ($service->validate($value, false)) {
            return true;
        }
        return false;
    }
}
