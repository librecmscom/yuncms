<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Client;

/**
 * OAuth 客户端
 *
 * @property int $id ID
 * @property string $name 应用名称
 * @property string $description 简介
 * @property string $website 主页
 * @property string $logo_path Logo 文件路径
 * @property string $secret 密钥
 * @property string $provider 服务商
 * @property string $redirect 授权跳转地址
 * @property boolean $personal_access_client 是否个人访问客户端
 * @property boolean $password_client 是否密码访问客户端
 * @property boolean $revoked 是否撤销
 * @property Carbon $created_at 创建时间
 * @property Carbon $updated_at 更新时间
 *
 * @property-read string $logo Logo Url
 *
 * @author Tongle Xu <xutongle@gmail.com>
 */
class PassportClient extends Client
{
    use Traits\DateTimeFormatter;

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [
        'personal_access_client' => false,
        'password_client' => false,
        'revoked' => false
    ];

    /**
     * 获取Logo存储位置
     *
     * @return string|null
     */
    public function getLogoAttribute(): ?string
    {
        if (!empty($value)) {
            return Storage::disk()->url($value);
        }
        return null;
    }

    /**
     * 客户端是否应跳过授权提示
     *
     * @return bool
     */
    public function skipsAuthorization(): bool
    {
        return $this->firstParty();
    }
}
