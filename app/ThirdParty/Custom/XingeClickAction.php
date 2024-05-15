<?php

namespace App\ThirdParty\Custom;

class XingeClickAction
{
    /**
     * 动作类型
     * @param int $actionType 1 打开activity或app本身，2 打开url，3 打开Intent
     */
    const TYPE_ACTIVITY = 1;
    const TYPE_URL = 2;
    const TYPE_INTENT = 3;

    private $actionType;

    private $url;

    private $intent;

    private $atyAttrIntentFlag;

    private $atyAttrPendingIntentFlag;

    public function __construct()
    {
        $this->atyAttrIntentFlag = 0;
        $this->atyAttrPendingIntentFlag = 0;
    }

    /**
     * @return mixed
     */
    public function getActionType()
    {
        return $this->actionType;
    }

    /**
     * @param mixed $actionType
     */
    public function setActionType($actionType): void
    {
        $this->actionType = $actionType;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url): void
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getIntent()
    {
        return $this->intent;
    }

    /**
     * @param mixed $intent
     */
    public function setIntent($intent): void
    {
        $this->intent = $intent;
    }

    public function toJson(): array
    {
        $ret = [
            'action_type' => $this->getActionType(),
            'intent' => $this->getIntent(),
            'url' => $this->getUrl(),
        ];
        if (isset($this->atyAttrIntentFlag)) {
            $ret['aty_attr']['if'] = $this->atyAttrIntentFlag;
        }
        if (isset($this->atyAttrPendingIntentFlag)) {
            $ret['aty_attr']['pf'] = $this->atyAttrPendingIntentFlag;
        }
        return $ret;
    }
}
