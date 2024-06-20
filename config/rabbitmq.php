<?php
use App\Common\Components\RabbitMQ\RabbitMQ;

/**
 * rabbitmq 配置
 * @package App\Common\Components\RabbitMQ
 */
return [
    // 测试队列
    'test' => [
        'exchange_type' => RabbitMQ::TYPE_DIRECT,
        'exchange_name' => 'test',
        'queue_name' => 'test',
        'route_key'  => 'message',
    ],

    // 测试延迟消息队列
    'delayed_message' => [
        'exchange_type' => RabbitMQ::TYPE_DELAY,
        'exchange_name' => 'delayed_message',
        'queue_name' => 'delayed_message',
        'route_key'  => 'message',
    ],
];
