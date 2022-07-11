<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

namespace App\Http\Requests\Api\V1;

use App\Http\Requests\FormRequest;

/**
 * 检测账户请求
 *
 * @property string|null $name 用户名称
 * @property string|null $email 用户邮箱
 * @property string|null $phone 用户手机
 */
class CheckAccountRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => ['required_without_all:email,phone'],
            'email' => ['required_without_all:name,phone'],
            'phone' => ['required_without_all:name,email', 'phone'],
        ];
    }
}
