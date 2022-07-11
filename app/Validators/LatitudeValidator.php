<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

namespace App\Validators;

/**
 * Class LatitudeValidator
 *
 * @codeCoverageIgnore
 * @author Tongle Xu <xutongle@gmail.com>
 */
class LatitudeValidator
{
    public function validate($attribute, $value, $parameters, $validator): bool
    {
        if ($value < -90 || $value > 90) {
            return false;
        }
        return true;
    }
}
