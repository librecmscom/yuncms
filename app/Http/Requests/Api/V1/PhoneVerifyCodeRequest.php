<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

namespace App\Http\Requests\Api\V1;

use App\Http\Requests\FormRequest;

/**
 * 手机验证码
 *
 * @property-read string $phone 手机号
 * @property-read string $scene 场景
 *
 * @author Tongle Xu <xutongle@gmail.com>
 */
class PhoneVerifyCodeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'phone' => ['required', 'max:11', 'phone', 'phone_verify',],
            'scene' => ['string'],
        ];
    }
}
