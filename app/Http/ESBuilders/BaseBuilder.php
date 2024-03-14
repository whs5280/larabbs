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
    public function table(string $name): BaseBuilder
    {
        $this->params['index'] = $name;
        return $this;
    }

    /**
     * 查询的字段，默认全部
     *
     * @param array $field
     * @return $this
     */
    public function select(array $field): BaseBuilder
    {
        $this->params['_source'] = $field;

        return $this;
    }

    /**
     * 分页
     *
     * @param $size
     * @param $page
     * @return $this
     */
    public function paginate($size, $page): BaseBuilder
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
     * @return $this
     */
    public function orderBy($field, $direction): BaseBuilder
    {
        if (!isset($this->params['body']['sort'])) {
            $this->params['body']['sort'] = [];
        }
        $this->params['body']['sort'][] = [$field => $direction];

        return $this;
    }

    /**
     * 初始搜索请求带上 scroll 参数
     * @return $this
     */
    public function initScroll(int $page = 10 , string $scroll = '1m'): BaseBuilder
    {
        $this->params['scroll'] = $scroll;
        $this->params['size'] = $page;

        return $this;
    }

    /**
     * @param string $scrollId
     * @param string $scroll
     * @return string[]
     */
    public function continueScroll(string $scrollId, string $scroll = '1m'): array
    {
        return [
            'scroll_id' => $scrollId,
            'scroll'    => $scroll
        ];
    }

    /**
     * 返回构造的参数体
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
