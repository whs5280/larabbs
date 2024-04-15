<?php

namespace App\Package\QueryCache\Traits;

/**
 * 接口层的实现类
 */
trait QueryCacheModule
{
    protected $cachePrefix = 'DbQuery';

    private $cacheKey;

    private $cacheTime;

    private $cacheDriver = 'redis';

    private $cacheBaseTags;

    private $cacheAvoided = true;

    private $cacheSecure = false;

    private $model;

    public function getFromQueryCache(string $method = 'get', array $columns = ['*'])
    {
        $key = $this->getCacheKey($method);
        $cacheTime = $this->getCacheTime();
        $callback = $this->getQueryCacheCallback($method, $columns);
        $cache = $this->getCache();

        if ($cacheTime instanceof \DateTime || $cacheTime > 0) {
            return $cache->remember($key, $cacheTime, $callback);
        }

        return $cache->rememberForever($key, $callback);
    }

    /**
     * @param $method
     * @return string
     */
    public function getCacheKey($method): string
    {
        $key = $this->generateCacheKey($method);
        $prefix = $this->getCachePrefix();

        return sprintf('%s:%s', $prefix, $key);
    }

    public function generateCacheKey($method): string
    {
        $key = $this->generatePlainCacheKey($method);

        if (!$this->shouldCacheSecure()) {
            return $key;
        }

        return hash('sha256', $key);
    }

    public function generatePlainCacheKey(string $method = 'get'): string
    {
        $name = $this->connection->getName();

        // Count has no Sql, that's why it can't be used ->toSql()
        if ($method === 'count') {
            return sprintf('%s:%s:%s', $name, $method, serialize($this->getBindings()));
        }

        return sprintf('%s:%s:%s', $name, $method, $this->toSql().serialize($this->getBindings()));
    }

    public function getQueryCacheCallback(string $method = 'get', $columns = ['*']): \Closure
    {
        return function () use ($method, $columns) {
            $this->cacheAvoided = true;
            return $this->{$method}($columns);
        };
    }

    public function flushQueryCache(array $tags = []): bool
    {
        $cache = $this->getCacheDriver();

        if (!method_exists($cache, 'tags')) {
            return false;
        }

        if (!$tags) {
            $tags = $this->getCacheBaseTags();
        }

        foreach ($tags as $tag) {
            $this->flushQueryCacheWithTag($tag);
        }

        return true;
    }

    public function flushQueryCacheWithTag(string $tag): bool
    {
        $cache = $this->getCacheDriver();

        try {
            return $cache->tags($tag)->flush();
        } catch (\BadMethodCallException $e) {
            return $cache->flush();
        }
    }

    public function getCache()
    {
        $cache = $this->getCacheDriver();

        $tags = $this->getCacheBaseTags() ?: [];

        try {
            return $tags ? $cache->tags($tags) : $cache;
        } catch (\BadMethodCallException $e) {
            return $cache;
        }
    }

    public function getCachePrefix()
    {
        return $this->cachePrefix;
    }

    public function getCacheTime()
    {
        return $this->cacheTime;
    }

    public function getCacheDriver()
    {
        return app('cache')->driver($this->cacheDriver);
    }

    public function getCacheBaseTags()
    {
        return $this->cacheBaseTags;
    }

    public function shouldAvoidCache(): bool
    {
        return $this->cacheAvoided;
    }

    public function shouldCacheSecure(): bool
    {
        return $this->cacheSecure;
    }

    public function dontCache(bool $avoidCache = true)
    {
        $this->cacheAvoided = $avoidCache;
        return $this;
    }

    public function setCacheTime($time)
    {
        $this->cacheTime = $time;
        return $this;
    }

    public function setCacheTags(array $cacheTags = [])
    {
        $this->cacheBaseTags = $cacheTags;
        return $this;
    }

    public function setCachePrefix(string $prefix)
    {
        $this->cacheKey = $prefix;
        return $this;
    }

    public function setCacheDriver(string $driver)
    {
        $this->cacheDriver = $driver;
        return $this;
    }

    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }
}
