<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

/**
 * 邮件验证码
 *
 * @author Tongle Xu <xutongle@gmail.com>
 */
class MailVerifyCode extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @var string 邮件验证码
     */
    protected string $verifyCode;

    /**
     * Create a new message instance.
     *
     * @param string $verifyCode
     */
    public function __construct(string $verifyCode)
    {
        $this->verifyCode = $verifyCode;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject(Lang::get('Email verification code :appName', ['appName' => config('app.name')]));
        return $this->markdown('emails.verify_code')->with([
            'verifyCode' => $this->verifyCode
        ]);
    }
}
