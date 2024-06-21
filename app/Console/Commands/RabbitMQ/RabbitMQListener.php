<?php

namespace App\Console\Commands\RabbitMQ;

use App\Common\Helpers\RabbitMQHelper;
use Illuminate\Console\Command;

class RabbitMQListener extends Command
{
    /**
     * @var string
     */
    protected $signature = 'rabbitmq:listen {name}';

    /**
     * @var string
     */
    protected $description = '监听队列';

    public function handle()
    {
        $this->description = "监听队列 {$this->argument('name')}";
        $this->info($this->description);
        $this->newLine();

        // 根据不同的参数配置不同的消费者
        switch ($this->argument('name')) {
            case 'test':
                $this->TestAMQPListener();
                break;
            case 'delayed_message':
                $this->DelayedMessageAMQPListener();
                break;
        }
    }

    public function TestAMQPListener()
    {
        $callback = function ($message){
            $isDone = false;
            $errorMsg = '';
            try {
                $this->info($message);
                $isDone = true;
            }catch (\Exception $e){
                $errorMsg = json_encode($e->getMessage());
            }
            print_message($isDone ? "DONE" : "ERROR", $message->body, $errorMsg);
            logger()->channel('mq')->info($isDone ? "DONE" : "ERROR", [$message, $errorMsg]);
        };

        try {
            RabbitMQHelper::TestAMQP()->listen($callback, true);
        } catch (\Throwable $e) {
            logger()->channel('mq')->error($e->getMessage());
        }
    }

    public function DelayedMessageAMQPListener()
    {
        $callback = function ($message){
            $isDone = false;
            $errorMsg = '';
            try {
                $this->info($message);
                $isDone = true;
            }catch (\Exception $e){
                $errorMsg = json_encode($e->getMessage());
            }
            print_message($isDone ? "DONE" : "ERROR", $message->body, $errorMsg);
            logger()->channel('mq')->info($isDone ? "DONE" : "ERROR", [$message, $errorMsg]);
        };

        try {
            RabbitMQHelper::DelayedMessageAMQP()->listen($callback, true);
        } catch (\Throwable $e) {
            logger()->channel('mq')->error($e->getMessage());
        }
    }
}
