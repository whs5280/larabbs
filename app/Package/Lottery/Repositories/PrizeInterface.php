<?php

namespace App\Package\Lottery\Repositories;

use App\Package\Lottery\Models\Prize;

interface PrizeInterface
{
    public function drawPrize(Prize $prize);
}
