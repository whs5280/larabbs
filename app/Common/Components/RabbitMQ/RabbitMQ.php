<?php

namespace App\Common\Components\RabbitMQ;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

/**
 * AMQP操作类
 * Class RabbitMQ
 * @package App\Common\Components\RabbitMQ
 */
class RabbitMQ
{
    CONST TYPE_DIRECT = 'direct';
    CONST TYPE_TOPIC = 'topic';
    CONST TYPE_FANOUT = 'fanout';
    CONST TYPE_HEADERS = 'headers';
    CONST TYPE_DELAY = 'delay';

    CONST ERROR_QUEUE_ROUTE_KEY = '#';

    /**
     * @var AMQPStreamConnection .MQ连接
     */
    protected $connection;

    /**
     * @var int .延迟时间
     */
    protected $ttl;

    /**
     * @var .交换机类型, 默认为 direct
     */
    protected $exchangeType = self::TYPE_DIRECT;

    /**
     * @var .交换机名称
     */
    protected $exchangeName;

    /**
     * @var .队列名称
     */
    protected $queueName;

    /**
     * @var .路由
     */
    protected $routeKey;

    /**
     * @var AMQPChannel[] .信道数组
     */
    protected $channels = [];

    /**
     * @return AMQPStreamConnection
     */
    public function init(): AMQPStreamConnection
    {
        $config = config('queue');
        $config = [
            'host' => $config['connections']['rabbitmq']['host'],
            'port' => $config['connections']['rabbitmq']['port'],
            'user' => $config['connections']['rabbitmq']['username'],
            'password' => $config['connections']['rabbitmq']['password'],
            'vhost' => $config['connections']['rabbitmq']['vhost'],
        ];

        return $this->connection = new AMQPStreamConnection($config['host'], $config['port'], $config['user'], $config['password'], $config['vhost']);
    }

    /**
     * @param $exchangeType
     * @return void
     */
    public function setExchangeType($exchangeType)
    {
        $this->exchangeType = $exchangeType;
    }

    /**
     * 设置交换机名称
     *
     * @param $exchange
     */
    public function setExchangeName($exchange)
    {
        $this->exchangeName= $exchange;
    }

    /**
     * 绑定路由key
     *
     * @param string $routeKey
     */
    public function setRouteKey(string $routeKey)
    {
        $this->routeKey = $routeKey;
    }

    /**
     * 设置queue名称
     *
     * @param string $queueName
     */
    public function setQueueName(string $queueName)
    {
        $this->queueName= $queueName;
    }

    /**
     * 异常队列
     *
     * @return string
     */
    public function getErrorQueueName(): string
    {
        return $this->queueName . '@error';
    }

    /**
     * 异常数据存放的交换机
     *
     * @return string
     */
    public function getErrorExchangeName(): string
    {
        return $this->exchangeName . '.error';
    }

    /**
     * 返回信道
     *
     * @param $channelId
     * @return AMQPChannel
     */
    public function getChannel($channelId = null): AMQPChannel
    {
        $index = $channelId ?: 'default';
        if (!array_key_exists($index, $this->channels)){
            $this->channels[$index] = $this->connection->channel($channelId);
        }
        return $this->channels[$index];
    }

    /**
     * 声明交换机
     */
    public function declareExchange()
    {
        // 获取信道
        $channel = $this->getChannel();
        // 声明名称
        $exchangeType = $this->exchangeType;
        $exchangeName = $this->exchangeName;
        $errorExchangeName = $this->getErrorExchangeName();
        // 指定交换机
        $channel->exchange_declare($exchangeName, $exchangeType, false, true, false);
        $channel->exchange_declare($errorExchangeName, $exchangeType, false, true, false);
    }

    /**
     * 声明队列
     */
    public function declareQueue()
    {
        $channel = $this->getChannel();
        // 声明名称
        $exchangeName = $this->exchangeName;
        $queueName = $this->queueName;
        $routeKey  = $this->routeKey;
        $errorExchangeName = $this->getErrorExchangeName();
        $errorQueueName    = $this->getErrorQueueName();
        // 死信队列
        $channel->queue_declare($queueName, false, true, false, false, false, new AMQPTable([
            'x-dead-letter-exchange'    => $errorExchangeName,
            'x-dead-letter-routing-key' => self::ERROR_QUEUE_ROUTE_KEY
        ]));
        // 队列和交换器绑定/绑定队列和类型
        $channel->queue_bind($queueName, $exchangeName, $routeKey);
        // 失败队列
        $channel->queue_declare($errorQueueName, false,true,false,false);
        $channel->queue_bind($errorQueueName, $errorExchangeName,self::ERROR_QUEUE_ROUTE_KEY);
    }

    /**
     * 数据插入到mq队列中（生产者）
     * 注：队列 和 交换机的 durable必须保持一致
     *
     * @param $messageBody .消息体
     * @param int $ttl .延迟时间
     */
    public function push($messageBody, int $ttl = 0)
    {
        try {
            $this->ttl = $ttl;
            // 构建通道（mq的数据存储与获取是通过通道进行数据传输的）
            $channel = $this->getChannel();
            // 指定交换机，若是路由的名称不匹配不会把数据放入队列中
            $this->declareExchange();
            // 声明队列, durable 代表持久化
            if ($this->exchangeType == self::TYPE_DIRECT) {
                $this->declareQueue();
            }
            // ['delivery_mode' => 2] 设置消息持久化
            $properties = [
                'content_type' => 'text/plain',
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            ];
            if ($this->ttl > 0) {
                $properties['application_headers'] = new AMQPTable(['x-delay' => $this->ttl]);
            } else {
                $properties['application_headers'] = new AMQPTable([]);
            }
            // 实例化消息推送类
            $message = $this->prepareMessage($messageBody, $properties);
            // 消息推送到路由名称为$exchange的队列当中
            $channel->basic_publish($message, $this->exchangeName, $this->routeKey);
            // 日志记录
            logger()->channel('mq')->info('生产者消息', [$messageBody]);
        } catch (\Exception $e) {
            logger()->channel('mq')->error('生产者队列 error message: ' . $e->getMessage());
        }
    }

    /**
     * 消费者：取出消息进行消费，并返回
     *
     * @param $callback .回调函数
     * @param bool $no_ack .若值为false，表示手动ACK应答,需要代码发送ACK.（消息确认机制）
     * @return bool
     * @throws \ErrorException
     */
    public function listen($callback, bool $no_ack = false): bool
    {
        logger()->channel('mq')->info('开始消费');
        // 连接到 RabbitMQ 服务器并打开通道
        $channel = $this->getChannel();
        // 声明交换机和队列
        $queue = $this->queueName;
        $this->declareExchange();
        $this->declareQueue();

        if ($no_ack) {
            // 获取消息队列的消息
            $msg = $channel->basic_get($queue);
            // ack机制，手动确定消息消费
            $channel->basic_ack($msg->delivery_info['delivery_tag']);
            //消息主题返回给回调函数
            $res = $callback($msg->body);
            if($res){
                logger()->channel('mq')->info('ack验证');
                //ack验证，如果消费失败了，从新获取一次数据再次消费
                $channel->basic_ack($msg->getDeliveryTag());
            }
        } else {
            /*
             * 流量控制 Specifies QoS
             * 消费者在开启acknowledge的情况下，对接收到的消息需要异步对消息进行确认
             * 由于消费者自身处理能力有限，从rabbitmq获取一定数量的消息后，希望rabbitmq不再将队列中的消息推送过来，
             * 当对消息处理完后（即对消息进行了ack，并且有能力处理更多的消息）再接收来自队列的消息
             * @param int $prefetch_size   最大unacked消息的字节数
             * @param int $prefetch_count  最大unacked消息的条数
             * @param bool $a_global       上述限制的限定对象，false限制单个消费者，true限制整个通道
             * @return mixed
            */

            // 当有多个消费者进行循环调度时，下面一行确保公平分发队列中的信息，每个消费者每次取一条
            $channel->basic_qos(0, 1, false);
            $channel->basic_consume($queue, '', false, $no_ack, false, false, $callback);
            while (count($channel->callbacks)) {
                $channel->wait();
            }
        }
        logger()->channel('mq')->info('ack消费完成');
        return true;
    }

    /**
     * 数据处理
     *
     * @param $message
     * @param null $properties
     * @return AMQPMessage
     * @throws \Exception
     */
    public function prepareMessage($message, $properties = null): AMQPMessage
    {
        if (empty($message)) {
            throw new \Exception('rabbitMq message can not be empty');
        }
        // json化 数组或对象
        if (is_array($message) || is_object($message)) {
            $message = json_encode($message, JSON_UNESCAPED_UNICODE);
        }
        return new AMQPMessage($message, $properties);
    }

    /**
     * 写入数据库
     * 如果有需要，可以在这里写入数据库
     * @param $ableId
     * @param $ableType
     * @param $event
     * @param int $status
     */
    public static function writeMessage($ableId, $ableType, $event, int $status = 0)
    {
        logger()->channel('mq')->info('写入日志', ['able_id' => $ableId, 'able_type' => $ableType, 'event' => $event, 'status' => $status]);
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * 关闭连接
     */
    public function close()
    {
        // 关闭消息推送资源
        foreach ($this->channels as $channel){
            $channel->close();
        }
        // 关闭mq资源
        if ($this->connection != null && $this->connection->isConnected()) {
            $this->connection->close();
        }
    }
}
