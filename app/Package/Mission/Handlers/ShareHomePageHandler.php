<?php

namespace App\Package\Mission\Handlers;

use App\Package\Mission\Contracts\Mission;
use App\Package\Mission\Contracts\MissionShare as Shareable;
use App\Package\Mission\Facades\MissionShare;

class ShareHomePageHandler extends AbstractHandler
{
    /**
     * 是否支持跳过校验
     *
     * @var bool
     */
    protected $skipCheck = false;

    public function check(): bool
    {
        if ($this->skipCheck) {
            return true;
        }
        list($startTime, $endTime) = $this->getPeriod();
        $sharer = $this->getMissionAcceptable();
        if ($sharer instanceof Shareable) {
            return MissionShare::hasShareRecord($sharer, $startTime, $endTime);
        }
        return false;
    }

    public function hasBtn(): bool
    {
        return true;
    }

    public function isJumpLink(): bool
    {
        return true;
    }

    public function isShare(): bool
    {
        return true;
    }

    protected function getPeriod(): array
    {
        $periodType = $this->getMission()->getMissionPeriodType();
        if ($periodType === Mission::PERIOD_ONCE) {
            return [null, null];
        }
        if ($periodType === Mission::PERIOD_DAILY) {
            return [now()->startOfDay(), now()->endOfDay()];
        }
        if ($periodType === Mission::PERIOD_WEEKLY) {
            return [now()->startOfWeek(), now()->endOfWeek()];
        }
        if ($periodType === Mission::PERIOD_MONTHLY) {
            return [now()->startOfMonth(), now()->endOfMonth()];
        }
        if ($periodType === Mission::PERIOD_YEARLY) {
            return [now()->startOfYear(), now()->endOfYear()];
        }
        return [null, null];
    }
}
