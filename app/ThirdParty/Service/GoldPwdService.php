<?php

namespace App\ThirdParty\Service;

/**
 * 加密服务
 */
class GoldPwdService
{
    CONST GOLD_KEY = '87C631A7A0BE7DC1';

    CONST METHOD = 'AES-128-CBC';

    CONST IV = 'b0f82e9c1a3b6d4f';

    /**
     * 加密数据
     *
     * @param $data
     * @return string
     */
    static function encrypt($data): string
    {
        if (is_string($data)) {
            return base64_encode(
                openssl_encrypt(
                    $data,
                    self::METHOD,
                    self::GOLD_KEY,
                    0,
                    self::IV
                ));
        }

        return base64_encode(
            openssl_encrypt(
                json_encode($data, JSON_UNESCAPED_UNICODE),
                self::METHOD,
                self::GOLD_KEY,
                0,
                self::IV
            ));
    }

    /**
     * 解密数据
     *
     * @param $encrypted
     * @param bool $isArray
     * @return mixed|string
     * @throws \Exception
     */
    static function decrypt($encrypted, bool $isArray = false)
    {
        if (!$json_str = openssl_decrypt(base64_decode($encrypted), self::METHOD, self::GOLD_KEY, 0, self::IV)) {
            throw new \Exception('解密失败！', 401);
        }

        if ($isArray) {
            return json_decode($json_str, true);
        }
        return $json_str;
    }
}
