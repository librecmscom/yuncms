<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

namespace App\Services;

use App\Models\PhoneCode;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Larva\Sms\Sms;
use Larva\Support\StringHelper;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;

/**
 * 手机验证码
 *
 * @author Tongle Xu <xutongle@gmail.com>
 */
class PhoneVerifyCodeService
{
    /**
     * @var string|int
     */
    protected string|int $phone;

    /**
     * 两次获取验证码的等待时间
     * @var int
     */
    protected int $waitTime;

    /**
     * 验证码有效期
     * @var int
     */
    protected int $duration;

    /**
     * 最长长度
     * @var int
     */
    protected int $length;

    /**
     * 静止验证码 功能测试时生成静止验证码
     * @var string|null
     */
    protected ?string $fixedVerifyCode;

    /**
     * 允许尝试验证的次数
     * @var int
     */
    protected int $testLimit;

    /**
     * 请求的IP
     * @var string
     */
    protected string $ip;

    /**
     * 验证码使用场景
     * @var string
     */
    protected string $scene;

    /**
     * 缓存Tag
     * @var string
     */
    private string $cacheTag;

    /**
     * Constructor.
     * @param int|string $phone
     * @param string $ip
     * @param string $scene
     */
    public function __construct(int|string $phone, string $ip, string $scene = 'default')
    {
        $this->phone = $phone;
        $this->ip = $ip;
        $this->scene = $scene;
        $this->duration = (int)settings('sms.duration', 10);
        $this->length = (int)settings('sms.length', 6);
        $this->waitTime = (int)settings('sms.wait_time', 60);
        $this->testLimit = (int)settings('sms.test_limit', 3);
        $this->cacheTag = 'sc:' . $phone;
        $this->fixedVerifyCode = null;
    }

    /**
     * 创建实例
     * @param int|string $phone
     * @param string $ip
     * @param string|null $scene
     * @return PhoneVerifyCodeService
     */
    public static function make(int|string $phone, string $ip, string $scene = null): PhoneVerifyCodeService
    {
        $scene = $scene ?? 'default';
        return new static($phone, $ip, $scene);
    }

    /**
     * 发送验证码
     * @return array
     */
    public function send(): array
    {
        //两次获取间隔小于 指定的等待时间
        if (($waitTime = time() - Cache::get($this->cacheTag . 'sendTime')) < $this->waitTime) {
            $verifyCode = $this->getVerifyCode();
            $data = [
                'hash' => $this->generateValidationHash($verifyCode),
                'waitTime' => $this->waitTime - $waitTime,
                'phone' => $this->phone,
                'scene' => $this->scene
            ];
        } else {
            $verifyCode = $this->getVerifyCode(true);
            if (!app()->environment('local')) {//生产环境才会发送
                try {
                    Sms::send($this->phone, new \App\Sms\VerifyCodeMessage([
                        'code' => $verifyCode,
                        'duration' => $this->duration,
                        'scene' => $this->scene
                    ]));
                } catch (NoGatewayAvailableException $exception) {
                    foreach ($exception->getExceptions() as $e) {
                        Log::error($e->getMessage());
                    }
                } catch (\Exception $exception) {
                    Log::error($exception->getMessage());
                }
            }
            PhoneCode::build($this->phone, $this->ip, $verifyCode, $this->scene);
            Cache::put($this->cacheTag . 'sendTime', time(), Carbon::now()->addSeconds($this->waitTime));
            $data = [
                'hash' => $this->generateValidationHash($verifyCode),
                'waitTime' => $this->waitTime,
                'phone' => $this->phone,
                'scene' => $this->scene
            ];
        }
        if (!app()->environment('production')) {
            $data['verify_code'] = $verifyCode;
        }
        return $data;
    }

    /**
     * 获取验证码
     * @param boolean $regenerate 是否重新生成验证码
     * @return string 验证码
     */
    public function getVerifyCode(bool $regenerate = false): string
    {
        if ($this->fixedVerifyCode !== null) {
            return $this->fixedVerifyCode;
        }
        $verifyCode = Cache::get($this->cacheTag . 'verifyCode');
        if ($verifyCode === null || $regenerate) {
            $verifyCode = StringHelper::randomInteger($this->length);
            Cache::put($this->cacheTag . 'verifyCode', $verifyCode, Carbon::now()->addMinutes($this->duration));
            Cache::put($this->cacheTag . 'verifyCount', 0, Carbon::now()->addMinutes($this->duration));
        }
        return $verifyCode;
    }

    /**
     * 验证输入，看看它是否与生成的代码相匹配
     * @param int|string $input user input
     * @param boolean $caseSensitive whether the comparison should be case-sensitive
     * @return boolean whether the input is valid
     */
    public function validate(int|string $input, bool $caseSensitive): bool
    {
        $code = $this->getVerifyCode();
        $valid = $caseSensitive ? ($input === $code) : strcasecmp($input, $code) === 0;
        $count = Cache::get($this->cacheTag . 'verifyCount', 0);
        $count = $count + 1;
        if ($valid || $count > $this->testLimit && $this->testLimit > 0) {
            PhoneCode::makeUsed($this->phone, $code);
            $this->getVerifyCode(true);
        }
        //更新计数器
        if (!$valid) {
            Cache::put($this->cacheTag . 'verifyCount', $count, Carbon::now()->addMinutes($this->duration));
        } else {//验证通过清楚计时器
            Cache::forget($this->cacheTag . 'verifyCount');
        }
        return $valid;
    }

    /**
     * 生成一个可以用于客户端验证的哈希。
     * @param string $code 验证码
     * @return string 用户客户端验证的哈希码
     */
    public function generateValidationHash(string $code): string
    {
        for ($h = 0, $i = strlen($code) - 1; $i >= 0; --$i) {
            $h += intval($code[$i]);
        }
        return (string)$h;
    }

    /**
     * 获取IP地址的发送次数
     * @return int
     */
    public function getIpSendCount(): int
    {
        return PhoneCode::getIpTodayCount($this->ip);
    }

    /**
     * 获取手机号发送次数
     * @return int
     */
    public function getPhoneSendCount(): int
    {
        return PhoneCode::getPhoneTodayCount($this->phone);
    }

    /**
     * 获取总发送次数
     * @return int
     */
    public function getSendCount(): int
    {
        return $this->getPhoneSendCount() + $this->getIpSendCount();
    }

    /**
     * 设置验证码的测试限制
     * @param int $testLimit
     * @return $this
     */
    public function setTestLimit(int $testLimit): PhoneVerifyCodeService
    {
        $this->testLimit = $testLimit;
        return $this;
    }

    /**
     * 设置验证场景
     * @param string $scene
     * @return $this
     */
    public function setScene(string $scene): PhoneVerifyCodeService
    {
        $this->scene = $scene;
        return $this;
    }

    /**
     * 设置两次获取验证码的等待时间
     * @param int $waitTime
     * @return $this
     */
    public function setWaitTime(int $waitTime): PhoneVerifyCodeService
    {
        $this->waitTime = $waitTime;
        return $this;
    }

    /**
     * 设置验证码有效期
     * @param int $duration 单位分钟
     * @return $this
     */
    public function setDuration(int $duration): PhoneVerifyCodeService
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * 设置验证码长度
     * @param int $length
     * @return $this
     */
    public function setLength(int $length): PhoneVerifyCodeService
    {
        $this->length = $length;
        return $this;
    }

    /**
     * 设置请求的IP地址
     * @param string $ip
     * @return $this
     */
    public function setIp(string $ip): PhoneVerifyCodeService
    {
        $this->ip = $ip;
        return $this;
    }

    /**
     * 设置静态验证码
     * @param string $code
     * @return $this
     */
    public function setFixedVerifyCode(string $code): PhoneVerifyCodeService
    {
        $this->fixedVerifyCode = $code;
        return $this;
    }
}
