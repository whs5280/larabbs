<?php

namespace App\Package\Mission\Contracts;

/**
 * 任务接受者接口
 */
interface MissionAcceptable
{
    /**
     * 返回任务接受者标识
     *
     * @return mixed
     */
    public function getMissionAcceptableKey();

    /**
     * 返回任务接受者类型
     *
     * @return mixed
     */
    public function getMissionAcceptableType();

    /**
     * 返回用户唯一标识
     *
     * @return mixed
     */
    public function getMissionAcceptableUniqueId();
}
