<?php

namespace App\Container;

use Jaeger\Config;
use OpenTracing\GlobalTracer;

class Jaeger
{
    static private $instance;

    static private $config;

    static private $trace;

    /**
     * Jaeger constructor.
     * @throws \Exception
     */
    /**
     * Jaeger constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        // 初始化配置
        $config = new Config(self::loadConfig(), self::$config['name']);
        $config->initializeTracer();

        // 全局的Tracer对象，这是一个单例对象
        self::$trace = GlobalTracer::get();
        return self::$trace;
    }


    /**
     * @return static
     */
    static function getInstance() : self
    {
        if (is_null(self::$instance)) {
            self::$config = config('logging.jaeger');
            self::$instance = new self();
        }
        return self::$instance;
    }


    /**
     * finish
     * @param $operationName
     */
    static function get($operationName)
    {
        self::$trace->startSpan($operationName)->finish();
    }


    /**
     * flush
     */
    static function flush()
    {
        self::$trace->flush();
    }


    /**
     * jaeger configs
     * @return array
     */
    static function loadConfig() : array
    {
        return [
            'sampler'       => [
                'type'  => \Jaeger\SAMPLER_TYPE_CONST,
                'param' => true,
            ],
            'logging'       => true,
            // tags记录需要记录的参数有哪些，自定义
            "tags"          => [
                'http.url'         => 'http.url',
                'http.method'      => 'http.method',
                'http.status_code' => 'http.status_code',

                'db.query'      => 'db.query',
                'db.statement'  => 'db.statement',
                'db.query_time' => 'db.query_time',

                'path'   => 'request.path',
                'method' => 'request.method',
                'header' => 'request.header',

                'status_code' => 'response.status_code',
            ],
            // jaeger的地址
            "local_agent"   => [
                "reporting_host" => self::$config['host'],
                "reporting_port" => self::$config['port'],
            ],
            // 使用udp协议传输数据
            'dispatch_mode' => Config::ZIPKIN_OVER_COMPACT_UDP,
        ];
    }
}
