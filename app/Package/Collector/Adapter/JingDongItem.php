<?php

namespace App\Package\Collector\Adapter;

use App\Package\Collector\Collector;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class JingDongItem extends Collector
{
    protected $requestUrl = 'https://api-gw.onebound.cn/jd/item_get/?';

    public function __construct($targetUrl)
    {
        $this->targetUrl = $targetUrl;
    }

    protected function getItemId($targetUrl)
    {
        $urlArr = parse_url($targetUrl);
        if(stripos($urlArr['host'], 'jd.com') !== false){
            preg_match('#/(\d+).html#i', $urlArr['path'], $id);
            if(empty($id[1])) return false;
            return $id[1];
        }
        return false;
    }

    protected function getItemDetail($itemId)
    {
        if (empty($itemId)) {
            return false;
        }
        $query = http_build_query([
            'key'     => config('collector.jingdong.key'),
            'num_iid' => $itemId,
            'lang'    => 'zh-CN',
            'secret'  => config('collector.jingdong.secret')
        ]);
        try {
            $response = (new Client(['verify' => false, 'timeout' => 3]))->get($this->requestUrl . $query);
            if ($response->getStatusCode() == 200) {
                $result = $response->getBody()->getContents();
                return json_decode($result, true);
            } else {
                logger()->channel('json')->error('JingDong -- 链接异常', ['code' => $response->getStatusCode()]);
                return false;
            }
        } catch (GuzzleException $e) {
            logger()->channel('json')->error('JingDong -- 链接异常', ['msg' => $e->getMessage()]);
            return false;
        }
    }

    protected function formatResult($result): ?array
    {
        if (empty($result)) return null;
        $item = $result['item'] ?? [];
        return [
            'title'		 =>  trim($item['title']),
            'price'      =>  $item['price'],
            'nick'       =>  $item['nick'],
            'item_url'   =>  $item['detail_url'],
            'shop_url'   =>  $item['seller_info']['zhuy'],
            'seller_id'  =>  $item['seller_info']['user_num_id'],
            'images'     =>  array_column($item['item_imgs'], 'url'),
            'desc_img'   =>  $item['desc_img'],
        ];
    }
}
