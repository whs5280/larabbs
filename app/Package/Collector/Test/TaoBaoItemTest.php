<?php

namespace App\Package\Collector\Test;

use App\Package\Collector\Adapter\TaoBaoItem;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\CreatesApplication;

class TaoBaoItemTest extends BaseTestCase
{
    use CreatesApplication;

    public function testCollect()
    {
        $url = 'https://detail.tmall.com/item.htm?abbucket=2&id=672030384045';
        $result = (new TaoBaoItem($url))->collect();

        $this->assertEquals(true, is_array($result));
        $this->assertEquals(true, isset($result['title']));
        $this->assertEquals(true, isset($result['price']));
        $this->assertEquals(true, isset($result['nick']));
        $this->assertEquals(true, isset($result['item_url']));
        $this->assertEquals(true, isset($result['shop_url']));
        $this->assertEquals(true, isset($result['seller_id']));
        $this->assertEquals(true, isset($result['images']));
        $this->assertEquals(true, isset($result['desc_img']));
        $this->assertEquals(true, is_array($result['images']));
        $this->assertEquals(true, is_array($result['desc_img']));
        $this->assertEquals(true, is_numeric($result['seller_id']));
    }
}
