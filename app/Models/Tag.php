<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

namespace App\Models;

use App\Events\Content\TagCreated;
use App\Models\Traits\DateTimeFormatter;
use App\Models\Traits\UseTableNameAsMorphClass;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Event;

/**
 * 标签表
 *
 * @property int $id
 * @property string $name
 * @property string $color
 * @property string $icon
 * @property Model $taggable
 *
 * @author Tongle Xu <xutongle@msn.com>
 */
class Tag extends Model
{
    use HasFactory, SoftDeletes, DateTimeFormatter, UseTableNameAsMorphClass;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tags';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'color', 'icon',
    ];

    /**
     * Perform any actions required before the model boots.
     *
     * @return void
     */
    protected static function booted(): void
    {
        static::created(function ($model) {
            Event::dispatch(new TagCreated($model));
        });
    }

    public function taggable(): MorphTo
    {
        return $this->morphTo();
    }
}
