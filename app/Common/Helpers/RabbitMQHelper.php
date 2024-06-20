<?php

namespace App\Common\Helpers;

use Illuminate\Contracts\Foundation\Application;

/**
 * 声明队列帮助类
 */
class RabbitMQHelper
{
    /**
     * 初始化队列
     * @param $name
     * @return Application|mixed
     * @throws \Throwable
     */
    public static function init($name)
    {
        $config = config('rabbitmq')[$name];
        throw_if(empty($config), "RabbitMQ config {$name} not found");

        $mq = app('MQ');
        $mq->setExchangeType($config['exchange_type']);
        $mq->setExchangeName($config['exchange_name']);
        $mq->setQueueName($config['queue_name']);
        $mq->setRouteKey($config['route_key']);
        return $mq;
    }

    /**
     * 测试队列
     * @return Application|mixed
     * @throws \Throwable
     */
    public static function TestAMQP()
    {
        return self::init('test');
    }

    /**
     * 延迟消息队列
     * @return Application|mixed
     * @throws \Throwable
     */
    public static function DelayedMessageAMQP()
    {
        return self::init('delayed_message');
    }
}
