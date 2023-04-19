<?php

namespace App\Sign\Support;

class RedisBitMap
{
    /**
     * 缓存key
     *
     * @var string
     */
    private $cacheKey;

    /**
     * 缓存前缀
     *
     * @var string
     */
    private $prefix;

    /**
     * Redis驱动
     *
     * @var
     */
    private $redis;


    /**
     * RedisBitMap constructor.
     *
     * @param $cacheKey
     */
    public function __construct($cacheKey)
    {
        $this->cacheKey = $cacheKey;
        $this->prefix = \Cache::store('redis')->getPrefix();
        $this->redis = \Cache::store('redis')->getRedis();
    }

    /**
     * 获取月份日 Redis 哈希表名称
     * @return string
     */
    public function getHashFromDateString()
    {
        return $this->cacheKey;
    }

    /**
     * 获取位图值
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->redis->getbit($this->getHashFromDateString(), $key);
    }

    /**
     * 设置位图值, key只支持整型
     * @param $key
     * @return mixed
     */
    public function set(int $key)
    {
        return $this->redis->setbit($this->getHashFromDateString(), $key, 1);
    }
}
