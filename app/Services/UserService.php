<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

declare(strict_types=1);
/**
 * This is NOT a freeware, use is subject to license terms
 */

namespace App\Services;

use App\Models\User;

/**
 * 用户服务助手
 *
 * @author Tongle Xu <xutongle@msn.com>
 */
class UserService
{
    /**
     * 用户名称是否存在
     *
     * @param string $name
     * @return bool
     */
    public function hasName(string $name): bool
    {
        return User::withTrashed()->where('name', $name)->exists();
    }

    /**
     * 邮箱是否存在
     *
     * @param string $email
     * @return bool
     */
    public function hasEmail(string $email): bool
    {
        return User::withTrashed()->where('email', $email)->exists();
    }

    /**
     * 检测邮箱是否存在
     *
     * @param int|string $phone
     * @return bool
     */
    public function hasPhone(int|string $phone): bool
    {
        return User::withTrashed()->where('phone', $phone)->exists();
    }
}
