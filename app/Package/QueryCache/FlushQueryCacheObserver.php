<?php

namespace App\Package\QueryCache;

use Illuminate\Database\Eloquent\Model;

class FlushQueryCacheObserver
{
    public function created(Model $model)
    {
        $this->invalidateCache($model);
    }

    public function updated(Model $model)
    {
        $this->invalidateCache($model);
    }

    public function deleted(Model $model)
    {
        $this->invalidateCache($model);
    }

    public function forceDeleted(Model $model)
    {
        $this->invalidateCache($model);
    }

    public function restored(Model $model)
    {
        $this->invalidateCache($model);
    }

    public function invalidateCache(Model $model)
    {
        $class = get_class($model);
        $tags = $class::getBaseTags();   // todo

        $class::flushQueryCache($tags);
    }
}
