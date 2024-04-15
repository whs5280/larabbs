<?php

namespace App\Package\QueryCache\Traits;

use App\Package\QueryCache\FlushQueryCacheObserver;
use App\Package\QueryCache\Query\Builder;

/**
 * Trait QueryCacheable
 * @method static Builder|static get()
 */
trait QueryCacheable
{
    /**
     * 重写 父类的 newBaseQueryBuilder 方法，实例化一个 缓存的 Builder
     * @return Builder
     */
    public function newBaseQueryBuilder(): Builder
    {
        $connection = $this->getConnection();

        $builder = new Builder(
            $connection,
            $connection->getQueryGrammar(),
            $connection->getPostProcessor()
        );

        $builder->dontCache(false);
        $builder->setModel(get_class($this));

        // 设置属性值
        if (property_exists($this, 'cacheTime')) {
            $builder->setCacheTime($this->cacheTime);
        }
        if (property_exists($this, 'cacheTags')) {
            $builder->setCacheTags($this->cacheTags);
        }
        if (property_exists($this, 'cachePrefix')) {
            $builder->setCachePrefix($this->cachePrefix);
        }
        if (property_exists($this, 'cacheDriver')) {
            $builder->setCacheDriver($this->cacheDriver);
        }

        return $builder;
    }

    public static function getFlushQueryCacheObserver(): string
    {
        return FlushQueryCacheObserver::class;
    }

    /**
     * 知识点
     * trait 的 property 无法被使用，因为不算是继承，它是一种代码复用机制
     * @return string[]
     */
    public function getCacheBaseTags(): array
    {
        return [
            (string) static::class,
        ];
    }
}
