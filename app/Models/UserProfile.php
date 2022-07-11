<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * 用户个人资料
 *
 * @property int $user_id 用户ID
 * @property int $gender 性别：0 未知 1 男性 2 女性
 * @property Carbon|null $birthday 生日
 * @property string|null $website 个人网站
 * @property string|null $intro 个人介绍
 * @property string|null $bio 个性签名
 *
 * @author Tongle Xu <xutongle@msn.com>
 */
class UserProfile extends Model
{
    use HasFactory;
    use Traits\BelongsToUser;

    //性别
    public const GENDER_UNKNOWN = 0;//保密
    public const GENDER_MALE = 1;//男性
    public const GENDER_FEMALE = 2;//女性
    public const GENDERS = [
        self::GENDER_UNKNOWN => '保密',
        self::GENDER_MALE => '男性',
        self::GENDER_FEMALE => '女性',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_profiles';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'user_id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'user_id'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id', 'gender', 'birthday', 'website', 'intro', 'bio'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'int',
        'gender' => 'int',
        'birthday' => 'datetime',
        'website' => 'string',
        'introduction' => 'string',
        'bio' => 'string',
    ];

    /**
     * The model's attributes.
     *
     * @var array
     */
    protected $attributes = [
        'gender' => self::GENDER_UNKNOWN,
    ];
}
