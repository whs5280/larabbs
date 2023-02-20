<?php

namespace App\Http\ESBuilders;

use Illuminate\Support\Facades\Artisan;

class TopicsBuilder extends BaseBuilder
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
        return 'topics' . self::INDEX_SUFFIX;
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
            'title' => [
                'type' => 'text',
                'analyzer' => 'ik_smart'
            ],
            'user_id' => [
                'type' => 'integer'
            ],
            'category' => [
                'type' => 'text',
                'analyzer' => 'douhao'
            ],
            'category_id' => [
                'type' => 'integer'
            ],
            'reply_count' => [
                'type' => 'integer'
            ],
            'view_count' => [
                'type' => 'integer'
            ],
            'last_reply_user_id' => [
                'type' => 'integer'
            ],
            'order' => [
                'type' => 'integer'
            ],
            'created_at' => [
                'type' => 'date',
                "format" => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis"
            ]
        ];
    }

    /**
     * 对应的脚本同步数据
     *
     * @param $indexName
     */
    public function rebuild($indexName)
    {
        Artisan::call('es:sync-tables ' . $indexName,);
    }
}
