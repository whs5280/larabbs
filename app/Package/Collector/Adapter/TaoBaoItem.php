<?php

namespace App\Package\Collector\Adapter;

use App\Package\Collector\Collector;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class TaoBaoItem extends Collector
{
    protected $requestUrl = 'https://api-gw.onebound.cn/taobao/item_get?';

    public function __construct($targetUrl)
    {
        $this->targetUrl = $targetUrl;
    }

    protected function getItemDetail($itemId)
    {
        if (empty($itemId)) {
            return false;
        }
        $query = http_build_query([
            'key'     => config('collector.taobao.key'),
            'num_iid' => $itemId,
            'lang'    => 'zh-CN',
            'secret'  => config('collector.taobao.secret'),
            'is_promotion' => 1,
        ]);
        try {
            $response = (new Client(['verify' => false, 'timeout' => 3]))->get($this->requestUrl . $query);
            if ($response->getStatusCode() == 200) {
                $result = $response->getBody()->getContents();
                return json_decode($result, true);
            } else {
                logger()->channel('json')->error('TaoBao -- 链接异常', ['code' => $response->getStatusCode()]);
                return false;
            }
        } catch (GuzzleException $e) {
            logger()->channel('json')->error('TaoBao -- 链接异常', ['msg' => $e->getMessage()]);
            return false;
        }
    }

    protected function getItemId($targetUrl)
    {
        $urlArr = parse_url($targetUrl);
        if (stripos($urlArr['host'], 'a.m.taobao.com') !== false) {
            preg_match('#/[a-zA-Z]?(\d+).htm#', $urlArr['path'], $id);
            return $id[1] ?? 0;
        }
        $queryArray = array();
        parse_str($urlArr['query'],$queryArray);
        return $queryArray['id'];
    }

    protected function formatResult($result): array
    {
        $item = $result['item'] ?? [];
        return [
            'title'		 =>  trim($item['title']),
            'price'      =>  $item['price'],
            'nick'       =>  $item['nick'],
            'item_url'   =>  $item['detail_url'],
            'shop_url'   =>  $item['seller_info']['zhuy'],
            'seller_id'  =>  abs($item['seller_id']),
            'images'     =>  array_column($item['item_imgs'], 'url'),
            'desc_img'   =>  $item['desc_img'],
        ];
    }
}
