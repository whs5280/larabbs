<?php

namespace App\ThirdParty\Custom;

class XingeMessage
{
    CONST TYPE_NOTIFICATION = 'notify';
    CONST TYPE_MESSAGE = 'message';

    private $title;

    private $content;

    private $expireTime;

    private $sendTime;

    private $type;

    private $multiPkg;

    private $action;

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getExpireTime()
    {
        return $this->expireTime;
    }

    /**
     * @param mixed $expireTime
     */
    public function setExpireTime($expireTime): void
    {
        $this->expireTime = $expireTime;
    }

    /**
     * @return mixed
     */
    public function getSendTime()
    {
        return $this->sendTime;
    }

    /**
     * @param mixed $sendTime
     */
    public function setSendTime($sendTime): void
    {
        $this->sendTime = $sendTime;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getMultiPkg()
    {
        return $this->multiPkg;
    }

    /**
     * @param mixed $multiPkg
     */
    public function setMultiPkg($multiPkg): void
    {
        $this->multiPkg = $multiPkg;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $action
     */
    public function setAction($action): void
    {
        $this->action = $action;
    }

    public function toJson(): array
    {
        $ret = [
            'title'   => $this->getTitle(),
            'content' => $this->getContent(),
        ];

        // 添加自定义消息体
        if (!empty($this->getAction())) {
            $ret['android'] = ['action' => $this->getAction()->toJson()];
        }
        return $ret;
    }
}
