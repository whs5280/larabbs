<?php

namespace App\Http\ESBuilders;

use Illuminate\Support\Facades\Artisan;

class StoreInfoBuilder extends BaseBuilder
{
    /**
     * 设置索引名称
     *
     * TopicsBuilder constructor.
     */
    public function __construct()
    {
        $this->table(self::getAliasName());
    }

    /**
     * 获取索引名称
     *
     * @return string
     */
    public static function getAliasName() : string
    {
        return 'stores' . self::INDEX_SUFFIX;
    }

    /**
     * 获取设置项
     *
     * @return array
     */
    public function getSettings() : array
    {
        return [
            'number_of_shards'   => 3,
            'number_of_replicas' => 2,
            'analysis' => [
                'analyzer' => [
                    'douhao' => [
                        'pattern' => ',',
                        'type'    => 'pattern',
                    ]
                ]
            ]
        ];
    }

    /**
     * 获取字段类型
     *
     * @return array
     */
    public function getProperties() : array
    {
        return [
            'name' => [
                'type' => 'text',
            ],
            'address' => [
                'type' => 'text'
            ],
            'location' => [
                'type' => 'geo_point'
            ],
        ];
    }

    /**
     * 对应的脚本同步数据
     *
     * @param $indexName
     */
    public function rebuild($indexName)
    {
        Artisan::call('es:sync-tables ' . $indexName);
    }
}
