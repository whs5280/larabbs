<?php

namespace App\Http\ESBuilders;

use Illuminate\Support\Facades\Artisan;

class UserBuilder extends BaseBuilder
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
        return 'users' . self::INDEX_SUFFIX;
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
            'id'   => [
                'type' => 'integer'
            ],
            'name' => [
                'type' => 'text',
            ],
            'email' => [
                'type' => 'text'
            ],
            'avatar' => [
                'type' => 'text'
            ],
            'introduction' => [
                'type' => 'text'
            ],
            'created_at' => [
                'type' => 'date',
                "format" => "yyyy-MM-dd HH:mm:ss||yyyy-MM-dd||epoch_millis"
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
        Artisan::call('es:sync-tables ' . $indexName,);
    }
}
