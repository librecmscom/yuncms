<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

namespace App\Http\Requests;

/**
 * 请求基类
 *
 * @method \App\Models\User|null user()
 * @codeCoverageIgnore
 * @author Tongle Xu <xutongle@gmail.com>
 */
abstract class Request extends \Illuminate\Http\Request
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        // Using policy for Authorization
        return true;
    }

    /**
     * 获取客户端端口
     *
     * @return int|null
     */
    public function getRemotePort(): ?int
    {
        $remotePort = $this->server('REMOTE_PORT');
        return $remotePort ? (int)$remotePort : null;
    }
}
