<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\HasApiTokens;

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
 * @property-read bool $emptyPassword 未设置密码
 * @property-read string|null $fuzzyPhone 模糊过的手机号
 *
 * @property UserProfile $profile 个人信息
 * @property UserExtra $extra 扩展信息
 *
 * @method static Builder active() 查询活动用户
 * @author Tongle Xu <xutongle@msn.com>
 */
class User extends Authenticatable implements MustVerifyEmail
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
            $model->profile()->create();//创建Profile
            $model->extra()->create();//创建Extra
        });
        static::forceDeleted(function (User $model) {
            $model->profile->delete();
            $model->extra->delete();
        });
    }

    /**
     * Get the profile relation.
     *
     * @return HasOne
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
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
     * 查询活的用户
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', '=', static::STATUS_NORMAL);
    }

    /**
     * 获取手机号
     *
     * @param \Illuminate\Notifications\Notification|null $notification
     * @return string|null
     */
    public function routeNotificationForPhone($notification): ?string
    {
        return $this->phone ?: null;
    }

    /**
     * 是否未设置密码
     *
     * @return boolean
     */
    public function getEmptyPasswordAttribute(): bool
    {
        return empty($this->getOriginal('password'));
    }

    /**
     * 获取头像Url
     *
     * @param string|null $value
     * @return string
     */
    public function getAvatarAttribute(?string $value): string
    {
        if (!empty($value)) {
            return Storage::disk()->url($value);
        }
        return asset(self::DEFAULT_AVATAR);
    }

    /**
     * Get the phone attribute
     *
     * @return string|null
     */
    public function getFuzzyPhoneAttribute(): ?string
    {
        return $this->phone ? substr_replace($this->phone, '****', 3, 4) : null;
    }

    /**
     * 设置头像
     *
     * @param string|null $avatarPath
     * @return bool
     */
    public function setAvatar(?string $avatarPath): bool
    {
        return $this->forceFill([
            'avatar' => $avatarPath
        ])->saveQuietly();
    }

    /**
     * 是否有头像
     *
     * @return boolean
     */
    public function hasAvatar(): bool
    {
        return !empty($this->getRawOriginal('avatar'));
    }

    /**
     * 发送邮箱验证通知
     *
     * @return void
     */
    public function sendEmailVerificationNotification(): void
    {
        if (!is_null($this->email)) {
            parent::sendEmailVerificationNotification();
        }
    }

    /**
     * Determine if the user has verified their phone number.
     *
     * @return bool
     */
    public function hasVerifiedPhone(): bool
    {
        return !is_null($this->phone_verified_at);
    }

    /**
     * Mark the given user's phone as verified.
     *
     * @return bool
     */
    public function markPhoneAsVerified(): bool
    {
        $status = $this->forceFill([
            'phone_verified_at' => $this->freshTimestamp(),
        ])->saveQuietly();
        Event::dispatch(new \App\Events\User\PhoneVerified($this));
        return $status;
    }

    /**
     * Mark the given user's email as verified.
     *
     * @return bool
     */
    public function markEmailAsVerified(): bool
    {
        $status = parent::markEmailAsVerified();
        Event::dispatch(new \App\Events\User\EmailVerified($this));
        return $status;
    }

    /**
     * Mark the given user's active.
     *
     * @return bool
     */
    public function markActive(): bool
    {
        return $this->forceFill([
            'status' => static::STATUS_NORMAL,
        ])->save();
    }

    /**
     * Mark the given user's disabled.
     *
     * @return bool
     */
    public function markDisabled(): bool
    {
        return $this->forceFill([
            'status' => static::STATUS_DISABLED,
        ])->save();
    }

    /**
     * Determine if the user has active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status == static::STATUS_NORMAL;
    }

    /**
     * 重置用户名
     *
     * @param string $name
     * @return void
     */
    public function resetUsername(string $name): void
    {
        if ($name != $this->name) {
            $this->forceFill(['name' => $name])->saveQuietly();
            Event::dispatch(new \App\Events\User\NameReset($this));
        }
    }

    /**
     * 重置用户密码
     *
     * @param string $password
     * @return void
     */
    public function resetPassword(string $password): void
    {
        $this->password = $password;
        $this->setRememberToken(\Illuminate\Support\Str::random(60));
        $this->saveQuietly();
        Event::dispatch(new \Illuminate\Auth\Events\PasswordReset($this));
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

    /**
     * 查找用户
     * @param string $name
     * @return mixed
     */
    public function findForPassport(string $name)
    {
        if (filter_var($name, FILTER_VALIDATE_EMAIL)) {
            return $this->active()->where('email', $name)->first();
        } else {
            return $this->active()->where('phone', $name)->first();
        }
    }

    /**
     * 随机生成一个用户标识
     *
     * @param string $name 用户名称
     * @return string
     */
    public static function generateName(string $name): string
    {
        if (static::query()->where('name', '=', $name)->exists()) {
            $row = static::query()->max('id');
            $name = $name . ++$row;
        }
        return $name;
    }
}
