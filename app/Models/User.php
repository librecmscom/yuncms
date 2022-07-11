<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\HasApiTokens;

/**
 * 用户模型
 *
 * @property int $id 用户ID
 * @property string $name 用户名称
 * @property string|null $email 用户邮箱
 * @property string|null $phone 手机号
 * @property string $avatar 头像
 * @property boolean $auth_status 认证状态：0 未提交 1 等待认证 2 认证通过 3 认证失败
 * @property int $status 用户状态
 * @property int $score 积分
 * @property string|null $password 用户密码
 * @property string|null $remember_token 记住我 Token
 * @property Carbon|null $phone_verified_at 手机验证时间
 * @property Carbon|null $email_verified_at 邮箱验证时间
 * @property Carbon $created_at 注册时间
 * @property Carbon $updated_at 最后更新时间
 * @property Carbon|null $deleted_at 删除时间
 *
 * @property UserExtra $extra 扩展信息
 * @author Tongle Xu <xutongle@msn.com>
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, SoftDeletes, Notifiable;
    use Traits\DateTimeFormatter;

    //默认头像
    public const DEFAULT_AVATAR = 'img/avatar.jpg';

    // 用户状态
    public const STATUS_NORMAL = 0;
    public const STATUS_DISABLED = 1;
    public const STATUSES = [
        self::STATUS_NORMAL => '正常',
        self::STATUS_DISABLED => '已禁用',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'phone', 'avatar', 'score', 'status', 'password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [
        'score' => 0,
        'status' => self::STATUS_NORMAL
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'int',
        'email' => 'string',
        'phone' => 'string',
        'avatar' => 'string',
        'score' => 'int',
        'status' => 'int',
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Perform any actions required after the model boots.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::created(function (User $model) {
            $model->extra()->create();//创建Extra
        });
        static::forceDeleted(function (User $model) {
            $model->extra->delete();
        });
    }

    /**
     * Get the extra relation.
     *
     * @return HasOne
     */
    public function extra(): HasOne
    {
        return $this->hasOne(UserExtra::class);
    }


    /**
     * 重置用户手机号
     * @param int|string $phone
     * @return bool
     */
    public function resetPhone(int|string $phone): bool
    {
        $status = $this->forceFill([
            'phone' => $phone,
            'phone_verified_at' => $this->freshTimestamp(),
        ])->saveQuietly();
        Event::dispatch(new \App\Events\User\PhoneReset($this));
        return $status;
    }

    /**
     * 重置用户邮箱
     * @param string $email
     * @return bool
     */
    public function resetEmail(string $email): bool
    {
        $status = $this->forceFill([
            'email' => $email,
            'email_verified_at' => $this->freshTimestamp(),
        ])->saveQuietly();
        Event::dispatch(new \App\Events\User\EmailReset($this));
        return $status;
    }
}
