<?php

namespace App\ThirdParty\Service;

class ImService
{
    public static function init() {
        return app('im');
    }

    public static function modifyGroupInfo($groupId, $attributes)
    {
        return self::init()->modifyGroupInfo($groupId, $attributes);
    }

    public static function getGroupInfo($groupId, $needMemberInfo = false)
    {
        return self::init()->getGroupInfo($groupId, $needMemberInfo);
    }

    public static function addGroupMember($groupId, $memberList)
    {
        return self::init()->addGroupMember($groupId, $memberList);
    }

    public static function sendGroupMessage($fromAccount, $groupId, $custom, $offlinePushInfo = array())
    {
        return self::init()->sendGroupMessage($fromAccount, $groupId, $custom, $offlinePushInfo);
    }

    public static function importAccount($uid, $nick = 'guest', $faceUrl = '')
    {
        $identifier = self::getIdentifier($uid);
        if (self::init()->isImportAccount($identifier)){
            return true;
        }
        return self::init()->importAccount($identifier, $nick, $faceUrl);
    }

    public static function getIdentifier($uid): string
    {
        $imPrefix = 'test';
        in_array(app()->environment(), ['pre', 'production']) && $imPrefix = 'prod';

        return $imPrefix.'_'.$uid;
    }
}
