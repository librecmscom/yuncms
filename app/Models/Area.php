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
 * 地区表
 *
 * @property int $id 地区ID
 * @property int $parent_id 父地区
 * @property string $name 名称
 * @property string $pinyin 拼音
 * @property int $city_code 区号
 * @property int $ad_code 区域编码
 * @property string $lng_lat 经纬度
 * @property int $order 排序
 * @property Carbon $created_at 注册时间
 * @property Carbon $updated_at 更新时间
 *
 * @author Tongle Xu <xutongle@msn.com>
 */
class Area extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'areas';
}
