<?php

namespace App\Traits;

/**
 * 静态单例模式
 * 私有化构造函数，防止外部调用
 * 私有化clone，防止被克隆
 * 场景：缓存，日志，数据库连接
 */
trait StaticInstance
{
    private static $instance;

    /**
     * 由于trait，使用static()
     * @return mixed
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    private function __construct()
    {

    }

    private function __clone()
    {
    }
}
