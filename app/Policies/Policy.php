<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * 授权策略基类
 * @codeCoverageIgnore
 * @author Tongle Xu <xutongle@gmail.com>
 */
class Policy
{
    use HandlesAuthorization;

//    public function before($user, $ability)
//    {
//        if ($user->isAdmin()) {
//            return true;
//        }
//    }
}
