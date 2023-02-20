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
        'index' => '',
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
     * 索引表名
     *
     * @param string $name
     * @return $this
     */
    public function table(string $name)
    {
        $this->params['index'] = $name;
        return $this;
    }

    /**
     * 查询的字段，默认全部
     *
     * @param array $field
     * @return $this|array
     */
    public function select(array $field)
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
    public function paginate($size, $page)
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
    public function orderBy($field, $direction)
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
    public function getParams()
    {
        return $this->params;
    }
}
