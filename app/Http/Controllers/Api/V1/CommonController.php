<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\MailVerifyCodeRequest;
use App\Http\Requests\Api\V1\PhoneVerifyCodeRequest;
use App\Models\Area;
use App\Services\MailVerifyCodeService;
use App\Services\PhoneVerifyCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Larva\Support\ISO3166;

/**
 * 公共接口
 *
 * @author Tongle Xu <xutongle@msn.com>
 */
class CommonController extends Controller
{
    /**
     * 短信验证码
     *
     * @param PhoneVerifyCodeRequest $request
     * @return array
     */
    public function phoneVerifyCode(PhoneVerifyCodeRequest $request): array
    {
        $verifyCode = PhoneVerifyCodeService::make($request->phone, $request->getClientIp(), $request->scene);
        return $verifyCode->send();
    }

    /**
     * 邮件验证码
     *
     * @param MailVerifyCodeRequest $request
     * @return array
     */
    public function mailVerifyCode(MailVerifyCodeRequest $request): array
    {
        $verifyCode = MailVerifyCodeService::make($request->email, $request->getClientIp());
        return $verifyCode->send();
    }

    /**
     * 国家列表接口
     *
     * @return array
     */
    public function country(): array
    {
        $items = ISO3166::$countries;
        $countries = [];
        foreach ($items as $code => $value) {
            $country = [
                'label' => ISO3166::country($code, App::getLocale()),
                'value' => $code
            ];
            $countries[] = $country;
        }
        return $countries;
    }

    /**
     * 地区接口
     *
     * @param Request $request
     * @return Collection
     */
    public function areas(Request $request): Collection
    {
        return Area::getAreas($request->get('id'), ['id', 'name']);
    }
}
