<?php

namespace App\Package\Mission\Contracts;

interface MissionShare
{
    public function getMissionId();

    public function getUserId();

    public function getShareUserId();

    public function getShareUserType();
}
