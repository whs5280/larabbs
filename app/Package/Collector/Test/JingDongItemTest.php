<?php

namespace App\Package\Collector\Test;

use App\Package\Collector\Adapter\JingDongItem;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\CreatesApplication;

class JingDongItemTest extends BaseTestCase
{
    use CreatesApplication;

    public function testCollect()
    {
        $url = 'https://item.jd.com/10055892531578.html?sdx=ehi-lLxFuZiE6JnIZIZUhcQlszGUDw4rsmpPtadHYtuDPe_RLJ1V4XTnrUDnUmGT#crumb-wrap';
        $result = (new JingDongItem($url))->collect();

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
