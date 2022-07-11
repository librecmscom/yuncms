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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * 地区表
 *
 * @property int $id 地区ID
 * @property int $parent_id 父地区
 * @property string $name 名称
 * @property int|null $city_code 区号
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

    /**
     * Get the children relation.
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(static::class, 'parent_id', 'id');
    }

    /**
     * Get the parent relation.
     *
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class);
    }

    /**
     * 获取地区
     *
     * @param int|string|null $parent_id
     * @param string[] $columns
     * @return Collection
     */
    public static function getAreas(int|string $parent_id = null, array $columns = ['id', 'name']): Collection
    {
        return static::query()->select($columns)->where('parent_id', $parent_id)->orderBy('order')->get();
    }
}
