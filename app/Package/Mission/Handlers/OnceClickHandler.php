<?php

namespace App\Package\Mission\Handlers;

/**
 * 单次点击任务处理器
 */
class OnceClickHandler extends AbstractHandler
{
    public function check(): bool
    {
        return true;
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
        return false;
    }
}
