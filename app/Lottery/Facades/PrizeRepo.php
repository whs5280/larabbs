<?php

namespace App\Lottery\Facades;

use Illuminate\Support\Facades\Facade;

class PrizeRepo extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'prize-repo';
    }
}
