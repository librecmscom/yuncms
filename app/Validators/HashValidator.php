<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

namespace App\Validators;

use Illuminate\Support\Facades\Hash;

/**
 * Class HashValidator
 *
 * @codeCoverageIgnore
 * @author Tongle Xu <xutongle@gmail.com>
 */
class HashValidator
{
    public function validate($attribute, $value, $parameters, $validator): bool
    {
        return Hash::check($value, $parameters[0]);
    }
}
