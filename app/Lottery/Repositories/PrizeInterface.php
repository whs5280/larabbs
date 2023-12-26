<?php

namespace App\Lottery\Repositories;

use App\Lottery\Models\Prize;

interface PrizeInterface
{
    public function drawPrize(Prize $prize);
}
