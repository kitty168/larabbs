<?php

namespace App\Observers;

use App\Models\Link;
use Cache;

class LinkObserver
{
    public function saved(Link $link)
    {
        // 清空指定键值的缓存
        Cache::forget($link->cache_key);
    }
}
