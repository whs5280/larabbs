<?php

namespace App\ThirdParty\Service;

use App\ThirdParty\Custom\XingeClickAction;
use App\ThirdParty\Custom\XingeMessage;
use App\ThirdParty\Custom\XingeMessageIOS;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * 腾讯信鸽服务, Android 已实现; IOS 还为处理
 *
 *  @package App\ThirdParty\Service
 */
class TpnsService
{
    CONST DEVICE_ANDROID = 'Android';
    const DEVICE_IOS = 'iOS';
    CONST EXPIRE_TIME = 60 * 60 * 72;

    CONST BASE_URL = 'https://api.tpns.tencent.com';
    CONST RESTAPI_PUSH = '/v3/push/app';

    protected $accessId = null;

    protected $accessKey = null;

    protected $secretKey = null;

    /**
     * @var string 账号前缀
     */
    protected $accountPrefix = null;

    /**
     * @var string 设备类型
     */
    protected $device = self::DEVICE_ANDROID;

    /**
     * @var array 接收方
     */
    protected static $accountList = [];

    /**
     * @var XingeMessage | XingeMessageIOS $message 消息体
     */
    protected static $message;

    /**
     * 初始化
     * @throws \Throwable
     */
    public function __construct()
    {
        $this->accessId = config('third.tpns.access_id');
        $this->accessKey = config('third.tpns.access_key');
        $this->secretKey = config('third.tpns.secret_key');
        $this->accountPrefix = config('third.tpns.account_prefix');
        throw_if(
            empty($this->accessId) || empty($this->accessKey) || empty($this->secretKey || empty($this->accountPrefix)),
            new \Exception("Parameter 'access_id' or 'access_key' or 'secret_key' is empty, please check your configuration!")
        );
    }

    public function getEnv(): string
    {
        return $this->accountPrefix ==  '' ? 'product' : 'dev';
    }

    /**
     * 设置消息
     *
     * @param string $title
     * @param string $content
     * @param array $jump
     * @param string $messageType
     * @param $expireTime
     * @return TpnsService
     * @throws \Throwable
     */
    public function message(string $title = '', string $content = '', array $jump = [], string $messageType = XingeMessage::TYPE_NOTIFICATION, $expireTime = self::EXPIRE_TIME): TpnsService
    {
        // 参数校验
        throw_if(
            !is_string($title) || !is_string($content) || !is_array($jump),
            new \Exception("parameter error!")
        );
        if ($this->device == self::DEVICE_ANDROID){
            self::$message = $this->getAndroidMessage($title, $content, $jump, $messageType, $expireTime);
        } else {
            self::$message = $this->getIOSMessage($title, $content, $jump, $messageType, $expireTime);
        }
        return $this;
    }

    /**
     * 设置接收消息用户组
     *
     * @param array $users 用户id数组
     * @return $this
     * @throws \Throwable
     */
    public function users(array $users = []): TpnsService
    {
        throw_if(!is_array($users) || empty($users), new \Exception("parameter error!"));

        $accountList = [];
        foreach ($users as $user) {
            if ($user > 0){
                $accountList[] = vsprintf('%s%s', [$this->accountPrefix, $user]);
            }
            self::$accountList = $accountList;
        }
        return $this;
    }

    /**
     * 推送
     *
     * @return false|mixed
     * @throws \Throwable
     */
    public function push()
    {
        if (empty(self::$message) || empty(self::$accountList)) {
            return false;
        }

        return $this->pushAccountList(self::$accountList, self::$message);
    }

    /**
     * 接口: 推送
     *
     * @param $accountList
     * @param $message
     * @return false|mixed
     * @throws \Throwable
     */
    public function pushAccountList($accountList, $message)
    {
        throw_if(empty($accountList) || empty($message), new \Exception("parameter error!"));

        throw_if(
            !($message instanceof XingeMessage) && !($message instanceof XingeMessageIOS),
            new \Exception("message is not android or ios!")
        );

        $params = [
            'audience_type'  =>  'account_list',
            'account_list'   =>  $accountList,
            'expire_time'    =>  $message->getExpireTime(),
            'message_type'   =>  $message->getType(),
            'message'        =>  $message->toJson(),
            'device_type'    =>  $this->device,
        ];
        if ($message instanceof XingeMessageIOS){
            $params['environment'] = $message->getEnvironment();
        }
        return $this->callRestful(vsprintf('%s%s', [self::BASE_URL, self::RESTAPI_PUSH]), $params);
    }

    /**
     * 发起 CURL请求
     *
     * @param $url
     * @param array $params
     * @return false|mixed
     */
    protected function callRestful($url, array $params = [])
    {
        $client = new Client(['verify' => false, 'timeout' => 3.0]);
        try {
            $response = $client->request('post', $url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => sprintf('Basic %s', base64_encode(vsprintf('%s:%s', [$this->accessId, $this->secretKey])))
                ],
                'json'   => $params,
            ]);
        } catch (GuzzleException $e) {
            logger()->info('tpns error info', ['error' => $e->getMessage()]);
            return false;
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * 构建Android消息体
     *
     * @param $title
     * @param $content
     * @param $jump
     * @param $messageType
     * @param $expireTime
     * @return XingeMessage
     */
    public function getAndroidMessage($title, $content, $jump, $messageType, $expireTime): XingeMessage
    {
        $message = new XingeMessage();
        $message->setTitle($title);
        $message->setContent($content);
        $message->setExpireTime($expireTime);
        $message->setSendTime(date('Y-m-d H:i:s'));
        $message->setType($messageType);
        $message->setMultiPkg(0);

        // 绑定自定义的消息体（点击消息后的跳转动作）
        if (!empty($jump)) {
            $action = new XingeClickAction();
            $action->setActionType(XingeClickAction::TYPE_INTENT);
            $message->setAction($action);
        }

        return $message;
    }

    /**
     * 构建IOS消息体
     *
     * @param $title
     * @param $content
     * @param $jump
     * @param $messageType
     * @param $expireTime
     * @return XingeMessageIOS
     */
    public function getIOSMessage($title, $content, $jump, $messageType, $expireTime): XingeMessageIOS
    {
        $message = new XingeMessageIOS();
        $message->setType($messageType);
        $message->setTitle($title);
        $message->setContent($content);
        $message->setExpireTime($expireTime);
        $message->setEnvironment($this->getEnv());

        if (!empty($jump)){
            $message->setCustom(json_encode($jump));
        }

        return $message;
    }
}
