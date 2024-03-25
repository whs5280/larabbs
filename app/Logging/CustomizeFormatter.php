<?php

namespace App\Logging;

use Illuminate\Log\Logger;
use Monolog\Formatter\LineFormatter;

class CustomizeFormatter
{
    /**
     * 自定义给定的日志记录器实例。
     */
    public function __invoke(Logger $logger): void
    {
        foreach ($logger->getLogger()->getHandlers() as $handler) {
            //dd($handler instanceof \Monolog\Handler\StreamHandler);
            $handler->setFormatter(new LineFormatter(
                '{"datetime": "%datetime%", "level_name": "%level_name%", "message": "%message%", "context": "%context%"}' . PHP_EOL,
                'Y-m-d H:i:s'
            ));
        }
    }
}
