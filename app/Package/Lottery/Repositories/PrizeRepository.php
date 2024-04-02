<?php

namespace App\Package\Lottery\Repositories;

use App\Package\Lottery\Models\Prize;

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
