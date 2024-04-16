<?php

namespace App\Package\Collector;

use App\Package\Collector\Adapter\JingDongItem;
use App\Package\Collector\Adapter\TaoBaoItem;

/**
 * 采集器工厂
 */
class CollectorFactory
{
    /**
     * 采集器字典
     * 有新增的采集器，请在此处添加
     *
     * @var string[]
     */
    protected static $domains = [
        'jd.com'     => JingDongItem::class,
        'taobao.com' => TaoBaoItem::class,
        'tmall.com'  => TaoBaoItem::class,
    ];

    /**
     * 根据链接创建采集器
     *
     * @throws \Exception
     */
    public static function create($targetUrl)
    {
        if (!$targetUrl) return null;

        foreach (self::$domains as $domain => $className)
        {
            if (strpos($targetUrl, $domain) !== false)
            {
                return new $className($targetUrl);
            }
        }

        throw new \Exception('can not find the adapter');
    }
}
