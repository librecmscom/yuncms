<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

namespace App\Validators;

/**
 * 域名验证
 *
 * @codeCoverageIgnore
 * @author Tongle Xu <xutongle@gmail.com>
 */
class DomainValidator
{
    public function validate($attribute, $value, $parameters, $validator): bool
    {
        if (!preg_match(config('system.domain_rule'), $value)) {
            return false;
        }
        return true;
    }
}
