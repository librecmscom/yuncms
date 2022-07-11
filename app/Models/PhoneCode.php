<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * 短信验证码
 *
 * @property int $id
 * @property string $scene 验证场景
 * @property string $phone 手机号
 * @property string $code 验证码
 * @property string $ip IP地址
 * @property int $state 使用状态
 * @property Carbon $usage_at 使用时间
 * @property Carbon $send_at 发送时间
 * @property User $user
 *
 * @author Tongle Xu <xutongle@gmail.com>
 */
class PhoneCode extends Model
{
    use HasFactory;
    use Traits\DateTimeFormatter;

    //使用状态
    public const USED_STATE = 1;
    public const CREATED_AT = 'send_at';
    public const UPDATED_AT = null;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'phone_codes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'scene', 'phone', 'code', 'ip', 'state', 'usage_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'id' => 'int',
        'send_at' => 'datetime',
        'usage_at' => 'datetime',
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'phone', 'phone');
    }

    /**
     * 记录验证码
     * @param int|string $phone
     * @param string $ip
     * @param string $code
     * @param string $scene 验证码场景
     * @return PhoneCode
     */
    public static function build(int|string $phone, string $ip, string $code, string $scene = 'default'): PhoneCode
    {
        return static::create(['phone' => $phone, 'ip' => $ip, 'code' => $code, 'scene' => $scene]);
    }

    /**
     * 标记为已使用
     * @param int|string $phone
     * @param string $code
     * @return bool
     */
    public static function makeUsed(int|string $phone, string $code): bool
    {
        $verifyCode = static::query()->where('phone', $phone)->where('code', $code)->first();
        $verifyCode?->update(['state' => static::USED_STATE, 'usage_at' => $verifyCode->freshTimestamp()]);
        return true;
    }

    /**
     * 获取今日发送总数
     * @param int|string $phone
     * @param string $ip
     * @return int
     */
    public static function getTodayCount(int|string $phone, string $ip): int
    {
        return static::getIpTodayCount($ip) + static::getPhoneTodayCount($phone);
    }

    /**
     * 获取IP今日发送总数
     * @param string $ip
     * @return int
     */
    public static function getIpTodayCount(string $ip): int
    {
        return static::query()
            ->where('ip', $ip)
            ->whereDay('send_at', Carbon::today())
            ->count();
    }

    /**
     * 获取今日发送总数
     * @param string|int $phone
     * @return int
     */
    public static function getPhoneTodayCount(int|string $phone): int
    {
        return static::query()
            ->where('phone', $phone)
            ->whereDay('send_at', Carbon::today())
            ->count();
    }
}
