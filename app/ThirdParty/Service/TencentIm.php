<?php

namespace App\ThirdParty\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * 腾讯云IM服务
 *
 * @package App\ThirdParty\Service
 */
class TencentIm
{
    public $sdkAppId = null;

    public $host = 'console.tim.qq.com';

    const USER_SIGN_URL = "http://apifc.yidoutang.com/usersig/getsig";  // 获取IM用户签名API，serverless部署

    /**
     * 初始化
     * @throws \Throwable
     */
    public function __construct()
    {
        $this->sdkAppId = config('third.im.app_key');
        throw_if(empty($this->sdkAppId), new \Exception("Parameter 'sdk_app_id' is empty, please check your configuration!"));
    }

    /**
     * 导入账号
     *
     * @param string $identifier 用户名
     * @param string $nick       用户昵称
     * @param string $faceUrl    用户头像 URL
     * @return bool
     */
    public function importAccount(string $identifier = '', string $nick = '', string $faceUrl = ''): bool
    {
        if (empty($identifier)){
            return false;
        }

        $action = "v4/im_open_login_svc/account_import";
        $params = [
            'Identifier' => $identifier,
            'Nick' => $nick,
            'FaceUrl' =>  $faceUrl
        ];

        $response = $this->call('POST', $params, $action);

        if (isset($response['ActionStatus']) && $response['ActionStatus'] == 'OK'){
            return true;
        }

        logger()->info('import account failed', ['response' => $response]);
        return false;
    }

    /**
     * 查询帐号是否已经导入iM
     *
     * @param array $identifierArr
     * @return array
     */
    public function checkAccountList(array $identifierArr = []): array
    {

        if (empty($identifierArr)){
            return [];
        }
        $checkItem = [];
        foreach ($identifierArr as $identifier){
            $checkItem[] = ['UserID' => $identifier];
        }
        $action = "v4/im_open_login_svc/account_check";
        $params = [
            'CheckItem' => $checkItem
        ];
        $response = $this->call('POST', $params, $action);
        if (isset($response['ActionStatus']) && $response['ActionStatus'] == 'OK'){
            $resultItem = $response['ResultItem'];
            $result = [];
            foreach ($resultItem as $item){
                if ($item['ResultCode'] == 0){
                    $result[$item['UserID']] = ($item['AccountStatus'] == 'Imported') ? 1 :0;
                }else{
                    $result[$item['UserID']] = 0;
                }
            }
            return $result;
        }

        return [];
    }

    /**
     * 查询帐号是否已经导入iM
     *
     * @param $identifier
     * @return bool|mixed
     */
    public function isImportAccount($identifier)
    {
        $checkList = $this->checkAccountList([$identifier]);
        if (in_array($identifier, array_keys($checkList))){
            return $checkList[$identifier];
        }

        return false;
    }

    /**
     * 创建群聊
     *
     * @param string $groupId      自定义群组Id
     * @param string $type         [Private/Public/ChatRoom/AVChatRoom/BChatRoom]
     * @param int|null $adminUserId  全管理员Id
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function createGroup(string $groupId, string $type, ?int $adminUserId = 0): mixed
    {
        $action = "v4/group_open_http_svc/create_group";
        $params = [
            'Type' => $type,
            'GroupId' => $groupId,
            'Name' =>  $type.":".$groupId
        ];
        // 添加群管理员
        if ($adminUserId > 0){
            $params['Owner_Account'] = $adminUserId;
        }

        $response = $this->call('POST', $params, $action);
        if (isset($response['ActionStatus']) && $response['ActionStatus'] == 'OK'){
            return true;
        }

        logger()->info('create group failed', ['response' => $response]);
        return false;
    }

    /**
     * 获取群组详细资料
     * @param string $groupId
     * @param bool $needMemberInfo
     * @return array
     */
    public function getGroupInfo(string $groupId, bool $needMemberInfo = false): array
    {
        $action = "v4/group_open_http_svc/get_group_info";
        $params = [
            'GroupIdList' => [$groupId],
            'ResponseFilter' => [
                'GroupBaseInfoFilter' => [
                    'Type',
                    'Name',
                    'Introduction',
                    'Notification',
                    'FaceUrl',
                    'CreateTime',
                    'Owner_Account',
                    'MemberNum',
                    'MaxMemberNum',
                ],
            ]
        ];

        if ($needMemberInfo){
            $params['ResponseFilter']['MemberInfoFilter'] = ['Account'];
        }

        $response = $this->call('POST', $params, $action);
        if (isset($response['ActionStatus']) && $response['ActionStatus'] == 'OK'){
            return $response['GroupInfo'];
        }

        logger()->info('get group info failed', ['response' => $response]);
        return [];
    }

    /**
     * 发送IM群聊消息
     * @param string $fromAccount
     * @param string $groupId
     * @param $custom
     * @param array $offlinePushInfo
     * @return bool
     */
    public function sendGroupMessage(string $fromAccount, string $groupId, $custom, array $offlinePushInfo = array()): bool
    {
        $action = "v4/group_open_http_svc/send_group_msg";

        $msgBody[] = [
            'MsgType' => 'TIMCustomElem',
            'MsgContent' => [
                "Data"=>(!empty($custom['data']) ? $custom['data'] : ''),
                "Desc"=>(!empty($custom['desc']) ? $custom['desc'] : ''),
                "Ext"=>(!empty($custom['ext']) ? $custom['ext'] : ''),
                "Sound"=>"dingdong.aiff"
            ]
        ];

        $params = [
            'GroupId' => $groupId,
            'random'  =>  rand(0, 4294967295),
            'MsgBody' => $msgBody,
            'ForbidCallbackControl' => ['ForbidBeforeSendMsgCallback', 'ForbidAfterSendMsgCallback']
        ];

        // 离线消息处理
        if (!empty($offlinePushInfo)){
            $params['OfflinePushInfo'] = [
                'PushFlag'=>$offlinePushInfo['push_flag'],
                'Desc'=>$offlinePushInfo['desc'],
                'Ext'=>$offlinePushInfo['ext'],
                'Title'=>$offlinePushInfo['title'],
            ];
        }

        if (!empty($fromAccount)) {
            $params['From_Account'] = $fromAccount;
        }

        $response = $this->call('POST', $params, $action);
        if (isset($response['ActionStatus']) && $response['ActionStatus'] == 'OK'){
            return true;
        }

        logger()->info('send group message failed', ['response' => $response]);
        return false;
    }

    /**
     * 获取群组成员详细资料
     *
     * @param string $groupId 群聊ID
     * @param int $limit      消息内容
     * @param int $offset     消息内容
     * @return array
     */
    public function getGroupMemberInfo(string $groupId, int $offset = 0, int $limit = 300): array
    {
        $action = "v4/group_open_http_svc/get_group_member_info";
        $params = [
            'GroupId' =>$groupId,
            'Limit' =>  $limit,
            'Offset' => $offset,
            "MemberInfoFilter"=> [ // 需要获取哪些信息（Member_Account 被默认包含在其中），如果没有该字段则为群成员全部资料
                "Role",
                "JoinTime",
                "MsgSeq",
                "MsgFlag",
                "LastSendMsgTime",
                "ShutUpUntil",
                "NameCard"
            ]
        ];

        $response = $this->call('POST', $params, $action);

        if (isset($response['ActionStatus']) && $response['ActionStatus'] == 'OK'){
            return $response['MemberList'];
        }

        return [];
    }

    /**
     * 修改群基础资料
     * @param string $groupId
     * @param array $attribute
     * @return bool
     */
    public function modifyGroupInfo(string $groupId, array $attribute): bool
    {
        $action = "v4/group_open_http_svc/modify_group_base_info";
        $params = [
            'GroupId' => $groupId,
            'Name' => $attribute['name'],
            'Introduction' => $attribute['intro'],
            //'Notification' => $attribute['notice'],
            'FaceUrl' => $attribute['icon'],
        ];

        $response = $this->call('POST', $params, $action);
        if (isset($response['ActionStatus']) && $response['ActionStatus'] == 'OK'){
            return true;
        }

        logger()->info('modify group info failed', ['response' => $response]);
        return false;
    }

    /**
     * 添加群成员
     * @param string $groupId
     * @param array $memberList
     * @return bool
     */
    public function addGroupMember(string $groupId, array $memberList): bool
    {
        $action = "v4/group_open_http_svc/add_group_member";
        $memberList = array_map(function($item){
            return ['Member_Account' => ImService::getIdentifier($item)];
        }, $memberList);
        $params = [
            'GroupId' => $groupId,
            'Silence' => 0,
            'MemberList' => $memberList
        ];

        $response = $this->call('POST', $params, $action);
        if (isset($response['ActionStatus']) && $response['ActionStatus'] == 'OK'){
            return true;
        }

        logger()->info('add group member failed', ['response' => $response]);
        return false;
    }

    /**
     * 发送请求
     *
     * @param string $method   请求方法 [GET POST]
     * @param array $params   请求参数
     * @param string $action   操作的接口名称（公共参数）
     */
    private function call(string $method, array $params, string $action)
    {
        ksort($params);
        return self::callCurl($method, $this->generateRequestUrl($action), $params);
    }

    /**
     * 发起curl请求
     *
     * @param $method
     * @param $uri
     * @param $params
     * @return mixed
     * @throws GuzzleException
     */
    private static function callCurl($method, $uri, $params)
    {
        if ($method == "POST"){
            $data['json'] = $params;
        }elseif($method == "GET") {
            $data['query'] = $params;
        }

        // 发送curl请求
        $client = new Client(['timeout' => 10]);
        $response = $client->request($method, $uri, $data);

        return json_decode($response->getBody()->getContents(),true);
    }

    /**
     * 生成APi请求地址
     *
     * @param $action
     * @return string
     */
    private function generateRequestUrl($action): string
    {
        $params = [
            'sdkappid'   => $this->sdkAppId,
            'identifier' => 'admin',
            'usersig'    => $this->getAdminSig(),
            'random'     =>  rand(0, 4294967295),
            'contenttype' => 'json'
        ];

        return 'https://'.$this->host.'/'.$action.'?'.http_build_query($params);
    }

    /**
     * 获取管理员的Sign
     *
     * @return string
     * @throws GuzzleException
     */
    private function getAdminSig(): string
    {
        return $this->getUserSign('admin');
    }

    /**
     * 获取用户签名
     *
     * @param $identity
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getUserSign($identity): string
    {
        $client = new Client();
        $response = $client->request('POST', self::USER_SIGN_URL
            ,[
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'identifier' => $identity
                ]
            ]
        );

        return strval($response->getBody());
    }
}
