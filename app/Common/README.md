# RabbitMQ 使用说明
- `config/queue.php` 连接配置
- `config/rabbitmq.php` 交换机 | 队列相关配置
- `app/Common/Helpers/RabbitMQHelper.php` 队列的初始化、声明
- `app/Console/Commands/RabbitMQ/RabbitMQListener.php` 监听队列、消费者逻辑
