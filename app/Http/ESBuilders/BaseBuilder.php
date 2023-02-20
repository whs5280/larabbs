<?php

namespace App\Http\ESBuilders;


class BaseBuilder
{
    /**
     * 后缀
     */
    const INDEX_SUFFIX = '_online';

    /**
     * 查询参数
     *
     * @var array
     */
    protected $params = [
        'index' => 'posts',
        'type'  => '_doc',
        'body'  => [
            'query' => [
                'bool' => [
                    'filter'   => [],
                    'must'     => [],
                    'must_not' => [],
                    'should'   => [],
                ],
            ],
        ],
    ];

    /**
     * 查询的字段，默认全部
     *
     * @param array $field
     * @return $this|array
     */
    public function select(array $field) : array
    {
        $this->params['_source'] = $field;

        return $this;
    }

    /**
     * 分页
     *
     * @param $size
     * @param $page
     * @return $this|array
     */
    public function paginate($size, $page) : array
    {
        $this->params['body']['from'] = ($page - 1) * $size;
        $this->params['body']['size'] = $size;

        return $this;
    }

    /**
     * 排序
     *
     * @param $field
     * @param $direction
     * @return $this|array
     */
    public function orderBy($field, $direction) : array
    {
        if (!isset($this->params['body']['sort'])) {
            $this->params['body']['sort'] = [];
        }
        $this->params['body']['sort'][] = [$field => $direction];

        return $this;
    }

    /**
     * 返回构造的参数体
     *
     * @return array
     */
    public function getParams() : array
    {
        return $this->params;
    }
}
