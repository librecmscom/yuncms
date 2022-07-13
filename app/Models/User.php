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
 * @property string $username 用户名
 * @property string|null $email 用户邮箱
 * @property string|null $phone 手机号
 * @property string $name 用户名称
 * @property string $avatar 头像
 * @property string $status 用户状态
 * @property int $score 积分
 * @property string|null $password 用户密码
 * @property string|null $remember_token 记住我 Token
 * @property Carbon|null $last_active_at 最后活动时间
 * @property Carbon|null $phone_verified_at 手机验证时间
 * @property Carbon|null $email_verified_at 邮箱验证时间
 * @property Carbon $created_at 注册时间
 * @property Carbon $updated_at 最后更新时间
 * @property Carbon|null $deleted_at 删除时间
 *
 * @property-read bool $emptyPassword 是否未设置密码
 * @property-read string|null $displayStatus 显示状态
 * @property-read string|null $displayPhone 显示手机号
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
    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVATED = 'inactivated';
    public const STATUS_FROZEN = 'frozen';
    public const STATUSES = [
        self::STATUS_INACTIVATED => '未激活',
        self::STATUS_ACTIVE => '正常',
        self::STATUS_FROZEN => '已冻结',
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
        'username', 'email', 'phone', 'name', 'avatar', 'score', 'status', 'password'
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
        'status' => self::STATUS_ACTIVE
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'int',
        'username' => 'string',
        'email' => 'string',
        'phone' => 'string',
        'name' => 'string',
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
        return $query->where('status', '=', static::STATUS_ACTIVE);
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
     * 获取状态显示
     *
     * @return string
     */
    public function getDisplayStatusAttribute(): string
    {
        return self::STATUSES[$this->status ?? self::STATUS_ACTIVE];
    }

    /**
     * Get the phone attribute
     *
     * @return string|null
     */
    public function getDisplayPhoneAttribute(): ?string
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
            'status' => static::STATUS_ACTIVE,
        ])->save();
    }

    /**
     * Mark the given user's frozen.
     *
     * @return bool
     */
    public function markFrozen(): bool
    {
        return $this->forceFill([
            'status' => static::STATUS_FROZEN,
        ])->save();
    }

    /**
     * Determine if the user has active.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status == static::STATUS_ACTIVE;
    }

    /**
     * 刷新最后活动时间
     *
     * @return $this
     */
    public function refreshLastActiveAt(): static
    {
        $this->updateQuietly([
            'last_active_at' => Carbon::now(),
            'status' => self::STATUS_ACTIVE,
        ]);
        return $this;
    }

    /**
     * 重置用户名
     *
     * @param string $username
     * @return void
     */
    public function resetUsername(string $username): void
    {
        if ($username != $this->username) {
            $this->forceFill(['username' => $username])->saveQuietly();
            Event::dispatch(new \App\Events\User\UsernameReset($this));
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
     * @param string $username
     * @return mixed
     */
    public function findForPassport(string $username)
    {
        if (preg_match(config('system.phone_rule'), $username)) {
            return $this->active()->where('phone', $username)->first();
        } elseif (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            return $this->active()->where('email', $username)->first();
        } else {
            return $this->active()->where('username', $username)->first();
        }
    }

    /**
     * 随机生成一个用户标识
     *
     * @param string $username 用户名
     * @return string
     */
    public static function generateUsername(string $username): string
    {
        if (static::query()->where('username', '=', $username)->exists()) {
            $row = static::query()->max('id');
            $username = $username . ++$row;
        }
        return $username;
    }
}
