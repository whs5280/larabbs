<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Monolog\Handler\RotatingFileHandler;

/**
 *  监听SQL执行
 *  命令行: php artisan make:listen QueryListener
 */
class QueryListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle(object $event)
    {
        try {
            if (config('database.sql_debug') == 1) {
                $sql = str_replace("?", "'%s'", $event->sql);
                foreach ($event->bindings as $i => $binding) {
                    if ($binding instanceof \DateTime) {
                        $event->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
                    } else {
                        if (is_string($binding)) {
                            $event->bindings[$i] = "'$binding'";
                        }
                    }
                }
                $log = vsprintf($sql, $event->bindings);
                $log = $log.'  [ RunTime:'.$event->time.'ms ] ';
                (new \Monolog\Logger('sql'))->pushHandler(new RotatingFileHandler(storage_path('logs/sql/sql.log')))->info($log);
            }
        } catch (\Exception $exception) {
            logger()->channel('json')->error('SQL执行失败', [
                'message' => $exception->getMessage(),
                'file'    => $exception->getFile(),
                'line'    => $exception->getLine(),
                'sql'     => $event->sql,
                'bindings' => $event->bindings,
            ]);
        }
    }
}
