<?php

namespace App\Package\QueryCache\Contracts;

/**
 * 契约：接口层
 */
interface QueryCacheModuleInterface
{
    /**
     * @param string $method
     * @return string
     */
    public function generatePlainCacheKey(string $method = 'get'): string;

    /**
     * @param string $method
     * @param array $columns
     * return \Closure
     */
    public function getQueryCacheCallback(string $method = 'get', array $columns = ['*']);
}
