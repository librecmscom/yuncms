<?php
/**
 * This is NOT a freeware, use is subject to license terms
 */

namespace App\Models\Traits;

/**
 * 使用表名作为多态类名
 *
 * @author Tongle Xu <xutongle@msn.com>
 */
trait UseTableNameAsMorphClass
{
    public function getMorphClass(): string
    {
        return $this->getTable();
    }
}
