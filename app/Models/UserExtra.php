<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 用户扩展信息
 *
 * @property int $user_id 用户ID
 *
 * @author Tongle Xu <xutongle@msn.com>
 */
class UserExtra extends Model
{
    use HasFactory;
    use Traits\BelongsToUser;
    use Traits\DateTimeFormatter;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_extras';
}
