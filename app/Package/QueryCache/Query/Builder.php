<?php

namespace App\Package\QueryCache\Query;

use App\Package\QueryCache\Contracts\QueryCacheModuleInterface;
use App\Package\QueryCache\Traits\QueryCacheModule;
use Illuminate\Database\Query\Builder as BaseBuilder;
use Illuminate\Support\Arr;

class Builder extends BaseBuilder implements QueryCacheModuleInterface
{
    use QueryCacheModule;

    public function get($columns = ['*'])
    {
        return $this->shouldAvoidCache()
            ? parent::get($columns)
            : $this->getFromQueryCache('get', Arr::wrap($columns));
    }

    public function useWritePdo(): Builder
    {
        $this->dontCache();
        return $this;
    }
}
