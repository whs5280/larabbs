<?php

namespace App\Package\Lottery\Support;

class RedisList
{
    protected $redis;

    protected $key;

    public function __construct($key)
    {
        $this->redis = \Cache::store('redis')->getRedis();
        $this->key = $key;
    }

    public function push($value)
    {
        return $this->redis->lpush($this->key, $value);
    }

    public function batchPush($values)
    {
        return $this->redis->lpush($this->key, ...$values);
    }

    /**
     * 推荐使用管道批量插入
     * @param $values
     * @return mixed
     */
    public function pipelinePush($values)
    {
        $pipe = $this->redis->pipeline();
        foreach ($values as $value) {
            $pipe->lpush($this->key, $value);
        }
        return $pipe->exec();
    }

    public function pop()
    {
        return $this->redis->rpop($this->key);
    }

    public function count()
    {
        return $this->redis->llen($this->key);
    }

    public function clear()
    {
        return $this->redis->del($this->key);
    }
}
