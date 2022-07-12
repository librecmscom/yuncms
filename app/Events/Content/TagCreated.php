<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

namespace App\Events\Content;

use App\Models\Tag;
use Illuminate\Queue\SerializesModels;

/**
 * 标签创建成功
 *
 * @author Tongle Xu <xutongle@gmail.com>
 */
class TagCreated
{
    use SerializesModels;

    public Tag $tag;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Tag $tag)
    {
        $this->tag = $tag;
    }
}
