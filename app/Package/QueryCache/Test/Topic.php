<?php

namespace App\Package\QueryCache\Test;

use App\Package\QueryCache\Traits\QueryCacheable;

/**
 * Model 使用方式如下
 * 1、特质 QueryCacheable
 * 2、设置 $cacheTime 缓存时间
 * 3、设置 $cacheTags 缓存标签
 */
class Topic extends \App\Models\Topic
{
    use QueryCacheable;

    protected $cacheTime = 300;

    public function getCacheBaseTags(): array
    {
        return [
            'topicCache'
        ];
    }
}
