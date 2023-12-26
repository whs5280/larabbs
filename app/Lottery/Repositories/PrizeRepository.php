<?php

namespace App\Lottery\Repositories;

use App\Lottery\Models\Prize;

class PrizeRepository implements PrizeInterface
{
    public function __construct()
    {

    }

    public function drawPrize(Prize $prize)
    {
        $prize->decrement('stock');
    }
}
