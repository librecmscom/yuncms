<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

namespace App\Validators;

/**
 * Class LongitudeValidator
 *
 * @codeCoverageIgnore
 * @author Tongle Xu <xutongle@gmail.com>
 */
class LongitudeValidator
{
    public function validate($attribute, $value, $parameters, $validator): bool
    {
        if ($value < -180 || $value > 180) {
            return false;
        }
        return true;
    }
}
